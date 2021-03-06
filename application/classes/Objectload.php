<?php defined('SYSPATH') or die('No direct script access.');

class Objectload 
{
	public $_objectload_id;
	public $_user_id;
	public $_settings = array(
			"configs" 		=> NULL,
			"dictionaries"  => NULL
		);

	const SETTING_NAME = "massload_link";

	function __construct($user_id, $objectload_id = NULL)
	{
		$this->_user_id 	  = $user_id;
		if (!$objectload_id)
			$this->_objectload_id = self::initRecord($user_id);
		elseif ($objectload_id == 'last')
			$this->_objectload_id = ORM::factory('Objectload')->order_by("id","desc")->find()->id;
		else
			$this->_objectload_id = $objectload_id;
	}


	public function loadSettings($user_id = NULL)
	{
		if ($user_id)
			$user_id = $this->_user_id;

		$settings = array();
		$settings["configs"] = Kohana::$config->load('massload/bycategory');
		foreach ($settings["configs"] as $name => $config) {
			@list($settings["dict_".$name], $settings["form_".$name]) = Massload::get_dictionary($config, $user_id, $name);
		}
		
		return $this->_settings = $settings;
	}

	public function downloadLinks($category = NULL)
	{
		$links = Array();
		$user_settings = ORM::factory('User_Settings')
								->where("name","=",self::SETTING_NAME)
								->where("user_id","=",$this->_user_id)
								->order_by("id","desc")
								->find_all();
		foreach ($user_settings as $setting)
				$links[] = $setting->value;

		foreach ($links as $link)
		{
			$tmp = tempnam("/tmp", "imgurl");
			copy($link, $tmp);
			$pathtofile = $tmp;

			$avito = new Massload_Avito;
			$files = $avito->convert_file($pathtofile);

			//если задали какую категорию качаем
			if ($category)
					$this->saveObjectLoadFileRecord($category, $files[$category]);
			else 
				foreach ($files as $cat => $path)
					$this->saveObjectLoadFileRecord($cat, $path);
		}

	}

	public function saveStaticFile($_FILE, $category, $user_id = NULL)
	{
		if ($user_id)
			$user_id = $this->_user_id;

		$config = $this->getConfig($category);

		$f = new Massload_File();
		@list($filepath, $imagepath) = $f->init($_FILE, $user_id);

		return $this->saveObjectLoadFileRecord($category, $filepath);
	}

	public function saveObjectLoadFileRecord($category, $filepath)
	{
		$of = ORM::factory('Objectload_Files');
		$of->objectload_id = $this->_objectload_id;
		$of->category = $category;
		$of->path = $filepath;
		return $of->save();

	}

	public function saveTempRecordsByLoadedFiles()
	{
		$limit = NULL;
		$setting_limit = ORM::factory('User_Settings')
								->get_by_name($this->_user_id, "massload_limit")
								->find();
		if ($setting_limit->loaded())
			$limit = (int) $setting_limit->value;

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $this->_objectload_id)
				->find_all();
		foreach ($of as $file){	

			$f = ORM::factory('Objectload_Files', $file->id);	
			$f->table_name = $this->saveRecordsToTempTable($file->id, $limit);
			$f->save();
			
		}
	}

	public function saveRecordsToTempTable($object_file_id, &$limit)
	{
		$of = ORM::factory('Objectload_Files', $object_file_id);
		if (!$of->loaded())
			return FALSE;

		$category 	= $of->category;
		$path 		= $of->path;

		$config 	= $this->getConfig($category);
		$fields 	= array_merge($config["fields"], Objectload::getServiceFields());
		$table_name = Temptable::get_name(array($category, $this->_user_id));
		
		Temptable::create_table($table_name, $fields);

		$f = new Massload_File();		

		$counter = 0;

		$f->forEachRow($config, $path, function($row, $i) use ($fields, $table_name, &$counter, $limit){

				if (isset($limit) AND $counter >= $limit) {
					return 'break';
				}

				$t = ORM_Temp::factory($table_name);
				foreach ($fields as $field){
					$fname = str_replace ( "-", "___", $field["name"]);
					if ($row->{$field["name"]})
						$t->{$fname} = strip_tags(trim($row->{$field["name"]}));
				}
				$t->save();

				$counter = $counter + 1;
		});

		if (isset($limit))
			$limit = $limit - $counter;

		return $table_name;
		
	}

	public function forEachRecord($filters,  $callback)
	{
		$object_load_id  = $this->_objectload_id;

		$of = ORM::factory('Objectload_Files');

		$of = $of->where("objectload_id","=",$object_load_id)
				  ->where("table_name","IS NOT",NULL);

		if ($filters->category)
			  $of = $of->where("category","=",$filters->category);
				
		$of = $of->order_by("category", "asc")
					->find_all();

	    $common_counter = 1;
		foreach($of as $file){
			$fields = array_keys(ORM_Temp::factory($file->table_name)->list_columns());

	        $counter = 1;

	        $t_object = DB::select()->from("_temp_".$file->table_name);
	        if ($filters->number)
	        	$t_object = $t_object->where("external_id", "=", $filters->number);
	        elseif ($filters->notloaded == 1)
	        	$t_object = $t_object->where("loaded", "IS", NULL)->where("error", "IS", NULL);
	        elseif ($filters->witherror == 1)
	        	$t_object = $t_object->where("error", "IS NOT", NULL);

	        $records = $t_object->order_by("id","asc")->as_object()->execute();
	        $count   = $records->count();
			

			foreach ($records as $record)
			{
				$row = new Obj();
				foreach ($fields as $value) {
					$value_repl = str_replace ( "___", "-", $value);
					$row->{$value_repl} = $record->{$value};
				}

				$cc = new Obj(array(
											"common" => $common_counter,											
											"counter"=> $counter,
											"count"	 => $count,
											"of_id"  => $file->id
									));

				$return = $callback($row, $file->category, $cc);
				
				$common_counter++;
				$counter++;
				if ($return == 'break') 
					break;
				elseif ($return == 'continue') 
					continue;
				elseif (is_array($return)  AND $return["status"] == 'nochange'){
					$this->setRecordNochange($file->table_name, $row->id, $return["object_id"]);
					continue;
				}
				elseif (is_array($return)  AND $return["status"] == 'success')
					$this->setRecordLoaded($file->table_name, $row->id, $return["edit"], $return["object_id"]);
				elseif (is_array($return)  AND $return["status"] == 'error'){
					$this->setRecordError($file->table_name, $row->id, $return["text_error"]);
					continue;
				}
			}

		}
	}

	public function saveRowAsObject($row, &$config, &$dictionary, $user_id = NULL)
	{
		if ($user_id)
			$user_id = $this->_user_id;

		$record = (array) $row;
		$record = array_merge($record, $config["autofill"]);
		unset($record["id"]);

		$record = Massload::to_post_format($record, $config["id"], NULL, $config, $dictionary);

		$record = array_merge($record, $config["autofill"]);
		$record['set_no_company'] = $row->set_no_company;
		return Object::PlacementAds_ByMassLoad($record, $user_id);

	}

	public function saveMainPhoto($object_id)
	{
		$attachment = ORM::factory('Object_Attachment')
					->where("object_id","=",$object_id)
					->order_by("type", "asc")
					->find();
		if ( $attachment->loaded() ) {
			if ($attachment->type == 4) {
				$filename = $this->saveFile($attachment->url);
				if ($filename) {
					$attachment->type = 0;
					$attachment->filename = $filename;
					$attachment->save();
					$object = ORM::factory('Object', $object_id);
					$object->main_image_id = $attachment->id;
					$object->save();
				}
			} else {
				$object = ORM::factory('Object', $object_id);
				$object->main_image_id = $attachment->id;
				$object->save();
			}
			
		}
	}

	public function savePhotos($object_id, $files_str = "", $save_images_accepted)
	{
		$existed_files = array();
		$oa = ORM::factory('Object_Attachment')
			->where("object_id","=",$object_id)
			->find_all();
		foreach ($oa as $attachment) {
			$existed_files[$attachment->url] = array(
				"id" => $attachment->id,
				"type" => ($attachment->type == 0) ? "file" : (($attachment->type == 4) ? "url" : "other")
			);
		}

		$filesAndUrls = $this->getFilesAndUrls(explode(";", $files_str), array_keys($existed_files), $save_images_accepted);

		$this->saveFilesAndUrls($object_id, $filesAndUrls, $existed_files);

		return $filesAndUrls;
	}

	public function saveFilesAndUrls($object_id, $filesAndUrls, $fileinfo) {
		$add = array();
		$delete = array();
		$fdelete = array();
		foreach ($filesAndUrls as $item) {
			if ($item["action"] == "add") {
				$add[] = $item;
			}

			if ($item["action"] == "delete") {
				if ($fileinfo[ $item["url"] ]["type"] == "file") {
					$fdelete[] = $fileinfo[ $item["url"] ]["id"];
				} else {
					$delete[] = $fileinfo[ $item["url"] ]["id"];
				}
			}
		}

		if (count($fdelete) > 0) {
			$attachment = ORM::factory('Object_Attachment')
					->where("id","IN",$fdelete)
					->fdelete();
		}
		if (count($delete) > 0) {
			$attachment = ORM::factory('Object_Attachment')
					->where("id","IN",$delete)
					->delete_all();
		}
		if (count($add) > 0) {
			foreach ($add as $item) {
				$attachment = ORM::factory('Object_Attachment');
				$attachment->object_id = $object_id;
				$attachment->filename = $item["path"];
				$attachment->url = $item["url"];
				$attachment->type = ($item["type"] == "file")? 0: 4;
				$attachment->save();
			}
		}
	}

	public function getFilesAndUrls($images = array(), $existed_images = array(), $save_images_accepted)
	{
		$result = array();

		$images_to_add = array_diff($images, $existed_images);
		$images_to_delete = array_diff($existed_images, $images);

		$i = 0;
		foreach($images_to_add as $file){

			if ($save_images_accepted) {
				$filepath = $this->saveFile($file); 
				if ($filepath) {
					$result[] = array("action" => "add", "type" => "file", "path" => $filepath, "url" => $file);
				}
			} elseif ($i == 0 AND (count($existed_images) == 0 OR in_array($file, $images_to_delete) OR count($existed_images) - count($images_to_delete) == 0) ) {
				$filepath = $this->saveFile($file); 
				if ($filepath) {
					$result[] = array("action" => "add", "type" => "file", "path" => $filepath, "url" => $file);
				}
			} else {
				$result[] = array("action" => "add", "type" => "url", "path" => $file, "url" => $file);
			}
			$i++;
		}

		foreach($images_to_delete as $file){
			$result[] = array("action" => "delete", "path" => $file, "url" => $file);
		}

		return $result;
	}

	public function saveFile($filepath)
	{
		$tmp = tempnam("/tmp", "imgurl");
		try {
			if (copy($filepath, $tmp)) {

				$_file = Array(
						'tmp_name' => $tmp,
						'size' => filesize($tmp),
						'name' => $tmp,
						'type' => mime_content_type($tmp),
					);
				return Uploads::save($_file);
			}
		} catch (Exception $e) {
			Log::instance()->add(Log::NOTICE, "error:".$e->getMessage());
			return FALSE;
		}
	}

	public function setState($state = 0, $comment = NULL)
	{
		$ol = ORM::factory('Objectload', $this->_objectload_id)
					->set_state($state, $comment);
		return $state;
	}

	public function testFile()
	{
		$id 	 = $this->_objectload_id;
		$user_id = $this->_user_id;

		$cmd = "php index.php --task=Objectload --test=1 --user_id=".$user_id." --objectload_id=".$id;
		$proc = popen($cmd, 'r');
		pclose($proc);
	}

	public function getStatistic()
	{
		return $ol = ORM::factory('Objectload', $this->_objectload_id)
					->get_statistic();
	}

	public function unpublishExpired()
	{
		ORM::factory('Objectload_Files')
			->get_union_subquery_by_category($this->_objectload_id);
	}

	private function setRecordLoaded($table_name, $id, $edit = FALSE, $object_id = NULL)
	{
		$record = ORM_Temp::factory($table_name, $id);
		$record->loaded = 1;
		if ($edit)
			$record->edited = 1;
		$record->error 		= NULL;
		$record->text_error = NULL;
		$record->nochange 	= NULL;
		$record->object_id 	= $object_id;
		$record->save();
	}

	private function setRecordError($table_name, $id, $text_error)
	{
		$record = ORM_Temp::factory($table_name, $id);
		$record->error 		= 1;
		$record->text_error = $text_error;
		$record->save();
	}

	private function setRecordNochange($table_name, $id, $object_id = NULL)
	{
		$record = ORM_Temp::factory($table_name, $id);
		$record->loaded = NULL;
		$record->edited = NULL;
		$record->error 		= NULL;
		$record->text_error = NULL;
		$record->nochange 	= 1;
		$record->object_id 	= $object_id;
		$record->save();
	}

	public function sendReport($objectload_id = NULL, $direct_email = NULL)
	{
		if (!$objectload_id){
			$objectload_id  = $this->_objectload_id;
		}

		$objectload = ORM::factory('Objectload', $objectload_id);
		$user = ORM::factory('User',$objectload->user_id);

		$us = ORM::factory('User_Settings')
					->where("name","=","massload_key")
					->where("user_id","=", $user->id)
					->find();

		$key = ($us->loaded()) ? $us->value : FALSE;

		$massload_email = ORM::factory('User_Settings')
								->where("name","=","massload_email")
								->where("user_id","=",$objectload->user_id)
								->getprepared_all();
		
		if (!$objectload_id OR !count($massload_email) OR !$objectload->loaded())
			return;

		$common_stat = new Obj($objectload->get_statistic());
		$category_stat = array();

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $objectload_id)
				->find_all();

		foreach ($of as $file)
		{	
			$cfg = Kohana::$config->load('massload/bycategory.'.$file->category);
			$category_stat[$cfg["name"]] = array(
					"id" => $file->id,
					"title" => $cfg["name"],
					"stat" => new Obj($file->get_statistic()),
					"key" => $key
				);
		}

		$params = array(
		    'objectload' => $objectload, 
		    'common_stat' => $common_stat, 
		    'category_stat' => $category_stat,
		    'org_name' => $user->org_name,
		    'logo' => 'http://yarmarka.biz/images/logo.png'
		);

		
		$emails = array();
		foreach ($massload_email as $email) {
			array_push($emails, $email->value );
		}



		Email_Send::factory('massload_report')
				->to( ($direct_email) ? $direct_email : $emails )
				->set_params($params)
				->set_utm_campaign('massload_report')
				->send();

		return ($direct_email) ? $direct_email : $emails;
	}

	public static function getServiceFields()
	{

		return array(
				"nochange" => array(
					"name" => "nochange",
					"type" => "int",
				),
				"loaded"   => array(
					"name" => "loaded",
					"type" => "int",
				),
				"edited"   => array(
					"name" => "edited",
					"type" => "int",
				),
				"error"    => array(
					"name" => "error",
					"type" => "int",
				),
				"text_error" => array(
					"name" => "text_error",
					"type" => "text",
				),
				"object_id" => array(
					"name" => "object_id",
					"type" => "int",
				),
				"premium" => array(
					"name" => "premium",
					"type" => "int",
				)
			);
	}

	private static function initRecord($user_id)
	{
		$load = ORM::factory('Objectload');
		$load->user_id  = $user_id;
		$load->save();

		return $load->id;
	}

	public function getConfig($category)
	{
		return Kohana::$config->load('massload/bycategory.'.$category);
	}
}