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
		$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $this->_objectload_id)
				->find_all();
		foreach ($of as $file){	

			$f = ORM::factory('Objectload_Files', $file->id);	
			$f->table_name = $this->saveRecordsToTempTable($file->id);
			$f->save();
			
		}
	}

	public function saveRecordsToTempTable($object_file_id)
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

		$f->forEachRow($config, $path, function($row, $i) use ($fields, $table_name){
				$t = ORM_Temp::factory($table_name);
				foreach ($fields as $field){
					$fname = str_replace ( "-", "___", $field["name"]);
					if ($row->{$field["name"]})
						$t->{$fname} = strip_tags(trim($row->{$field["name"]}));
				}
				$t->save();
		});

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

	        $t_object = DB::select()->from("_temp_".$file->table_name)
	        ;
	        if ($filters->notloaded == 1)
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
					$this->setRecordNochange($file->table_name, $row->id);
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

		return Object::PlacementAds_ByMassLoad($record, $user_id);

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

	private function setRecordNochange($table_name, $id)
	{
		$record = ORM_Temp::factory($table_name, $id);
		$record->loaded = NULL;
		$record->edited = NULL;
		$record->error 		= NULL;
		$record->text_error = NULL;
		$record->nochange 	= 1;
		$record->save();
	}

	public function sendReport($objectload_id = NULL)
	{
		if (!$objectload_id){
			$objectload_id  = $this->_objectload_id;
		}

		$objectload = ORM::factory('Objectload', $objectload_id);
		$user = ORM::factory('User',$objectload->user_id);

		$massload_email = ORM::factory('User_Settings')
								->where("name","=","massload_email")
								->where("user_id","=",$objectload->user_id)
								->find();
		
		if (!$objectload_id OR !$massload_email->loaded() OR !$objectload->loaded())
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
					"org_name" => $user->org_name
				);
		}

		$subj = "Отчет по загрузке объявлений ";
		$email_params = array( 'objectload' => $objectload, 
							   'common_stat' => $common_stat, 
								'category_stat' => $category_stat);

		
		$msg = View::factory('emails/massload_report',$email_params);
		//Kohana::$log->add(Log::NOTICE, Debug::vars($email_params));
		Email::send($massload_email->value, Kohana::$config->load('email.default_from'), $subj, $msg);
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