<?php defined('SYSPATH') or die('No direct script access.');

class Massload 
{
	const MAX_COUNT_ERRORS = 10;
	const CONTACT_LENGTH = 10;

	const MARK_ERROR_TAG_OPEN = '&lt;span class="background-gray" &gt;&lt;b&gt;';
	const MARK_ERROR_TAG_CLOSE = "&lt;/b&gt;&lt;/span&gt;";

	const ROW_ERROR_TAG_OPEN = '&lt;span class="error1" &gt;';
	const ROW_ERROR_TAG_CLOSE = "&lt;/span&gt;";

	public function checkFile($file, $category, $user_id)
	{
		$errors = Array();

		$f = new Massload_File();

		@list($filepath, $imagepath) = $f->init($file, $user_id);

		$config = self::get_config($category);
		@list($dictionary, $form_dictionary) = self::get_dictionary($config, $user_id, $config["category"]);

		$count = 0;

		$f->forEachRow($config, $filepath, function($row, $i) use ($imagepath, &$errors, $config, &$count, $dictionary){

			//$string = join(",", array_values($row));

			/*if ($row->count() <> count($config["fields"]) )
			{
				$errors[] = '(Ошибка стр. '.$i.') Количество полей в строке не соответсвует требованиям для загрузки';
				//$errors[] = $string;
				return "continue";
			}*/

			$validation = Massload::init_validation($row, $i, $config, $dictionary, $imagepath);

			if ( ! $validation->check()){
				$errors[] = "</br>======";
				$validation_errors = $validation->errors('validation/massload');
				$errors = array_merge($errors, array_values($validation_errors));

				$row_errors = (array) $row;
				foreach ($validation_errors as $key_err=>$err)
					$row_errors[$key_err] = Massload::MARK_ERROR_TAG_OPEN.((string) $row->{$key_err}).Massload::MARK_ERROR_TAG_CLOSE;

				$errors[] = join("|", array_values($row_errors));
				
			}
			$count++;

			/*if (count($errors)>Massload::MAX_COUNT_ERRORS)
			{
				$errors[] = "</br>======";
				$errors[] = 'Найдено более '.Massload::MAX_COUNT_ERRORS.' ошибок. Проверка файла остановлена.';
				return "break";
			}*/

		});

		return Array($filepath, $imagepath, $errors, $count);
	}

	public function preProccess($filepath, $category, $user_id)
	{
		$ids = array();
		$f = new Massload_FileXml();

		$config = self::get_config($category);

		$massload_id = $f->createMassload($filepath, $user_id, $category);


		$f->forAll($filepath, function ($row, $i) use (&$ids, $massload_id){
			if ($row->external_id){
				$om = ORM::factory('Object_Massload');
				$om->external_id = $row->external_id;
				$om->massload_id = $massload_id;
				$om->save();
			}
		});

		$sub = DB::select('external_id')->from('object_massload')
						->join("massload", "left")
							->on("massload.id","=","massload_id")
						->where("path","=",$filepath)
						->where("user_id","=",$user_id);

		ORM::factory('Object')
				->where_open()
				->where('number', 'NOT IN', $sub)
					->or_where('number', 'IS', NULL)
				->where_close()
				->where('author', '=', $user_id)
				->where('category','=', $config["id"])
				->set('is_published', 0)
				->update_all();
	}

	public function saveStrings($pathtofile, $pathtoimage, $category, $step, $iteration, $user_id)
	{
		$f = new Massload_File();

		$massload_id = $f->createMassload($pathtofile, $user_id, $category);

		$objects = Array();
		$config = self::get_config($category);
		@list($dictionary, $form_dictionary) = self::get_dictionary($config, $user_id, $config["category"]);
		$category_id = $config["id"];

		$f->forRow($config, $pathtofile, $step, $iteration, function($row, $i) use ($massload_id, $pathtoimage, $config, $category_id, &$objects, $dictionary){

			/*if ($row->count() <> count($config["fields"]) )
			{
				return "continue";
			}*/

			$validation = Massload::init_validation($row, $i, $config, $dictionary, $pathtoimage);
			if (!$validation->check()) return "continue";	
			

			$record = Array();

			foreach ($row as $name=>$value) 
			{				
				if ($value <> "")
					$record[$name] = $value;				
			}	

			$record = Massload::to_post_format($record, $category_id, $pathtoimage, $config, $dictionary);

			$record = array_merge($record, $config["autofill"]);

			$objects[] = Object::PlacementAds_ByMassLoad($record, $massload_id);

		});

		return $objects;
	}

	private static function get_config($category)
	{
		return Kohana::$config->load('massload/bycategory.'.$category);
	}

	public static function init_validation($row, $i, $config, $dictionary, $pathtoimage)
	{
		$validation = Validation::factory((array) $row);

		$rules = Array();
		foreach ($row as $key=>$value)
		{
			if (!array_key_exists($key, $config["fields"])) continue;

			$config_key = new Obj($config["fields"][$key]);

			$valid_info 		= array(':value', $dictionary, $config_key->translate, $i, $value);
			$valid_info_contact = array(':value', $config_key->maxlength, $dictionary, $config_key->translate, $i, $value);
			$valid_info_dict 	= array(':value', $config_key->name, $dictionary, $config_key->translate, $i, $value);
			$valid_info_photo 	= array(':value', $pathtoimage, $config_key->translate, $i, $value);
			$valid_info_maxlength = array(':value', $config_key->maxlength, $dictionary, $config_key->translate, $i, $value);

			if ($config_key->required) 
				$validation->rule($key, 'not_empty', $valid_info);

			if ($config_key->type == "city"){
				$validation->rule($key, 'check_city_value', $valid_info);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "dict"){
				$validation->rule($key, 'check_dictionary_value', $valid_info_dict);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "contact")
			{
				$validation->rule($key, 'check_contact', $valid_info);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "integer")
			{
				$validation->rule($key, 'not_0', $valid_info);
				//$validation->rule($key, 'digit', $valid_info);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "external_id")
			{
				$validation->rule($key, 'not_0', $valid_info);
				$validation->rule($key, 'digit', $valid_info);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "textadv")
			{
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "numeric")
			{
				$validation->rule($key, 'numeric', $valid_info);
				$validation->rule($key, 'max_length', $valid_info_maxlength);
			}

			if ($config_key->type == "photo")
				$validation->rule($key, 'check_photo', $valid_info_photo);

			

		}

		return $validation;	
	}

	public static function to_post_format($record_fields, $category_id, $pathtoimage, $config, $dictionary)
	{
		//echo var_dump($dictionary);
		$return = Array();
		foreach($record_fields as $key=>$value)
		{
			if (!array_key_exists($key, $config["fields"])) continue;
			$urls   = Array();
			$type = $config["fields"][$key]['type'];
			switch ($type) {
				case 'city':
					$key = "city_id";
					$value = $dictionary[$type."_".$value];//ORM::factory('City')->by_title($value)->id;
								
				break;
				case 'dict':
					$value = $dictionary[$key."_".$value];					
					$key = "param_".ORM::factory('Reference')->by_category_and_attribute($category_id, $key);					
				break;
				case 'integer':
					$key = "param_".ORM::factory('Reference')->by_category_and_attribute($category_id, $key);					
				break;
				case 'photo':	
					$files = explode(";", $value);	
					$key = "userfile";
					$values = Array();
					$type_file = "";
					foreach($files as $value){	
						$filename = $pathtoimage.$value;
						if (filter_var($value, FILTER_VALIDATE_URL))
							$type_file = 'url';
						elseif (is_dir($filename."/"))
							$type_file = 'dir';
						elseif ( file_exists($filename) )
							$type_file = 'file';

						switch ($type_file) {
							case 'file':						
								$key = "userfile";					
								$values[] = self::save_photo($filename, $value);
							break;
							
							case 'dir':
								$files = glob($filename.'/*.{jpg,png,gif,bmp}', GLOB_BRACE);
								$key = "userfile";
								//$value = Array();
								foreach($files as $file) {	
									try {							
										$values[] = self::save_photo($file, $file);
									} catch (Exception $e) {}
								}
							break;

							case 'url':	
								$urls[] = array("title"=>$record_fields["external_id"], "url"=>$value);
								if (array_key_exists("external_id", $record_fields))
								{
									$oa = ORM::factory('Object_Attachment')
										->where("title","=",$record_fields["external_id"])
										->where("url","=",$value)
										->find();
									if ($oa->loaded())
									{
										$key = "userfile";					
										$values[] = $oa->filename;
										break;
									}

								}
								$tmp = tempnam("/tmp", "imgurl");
								try {
									if (copy($value, $tmp))
									{

										$key = "userfile";					
										$values[] = self::save_photo($tmp, $tmp);

									}
									

								} catch (Exception $e){
									Log::instance()->add(Log::NOTICE, "error:".$e->getMessage());	
								}
								//Log::instance()->add(Log::NOTICE, "filesize:".filesize($tmp));		
							break;
						}	
					}	

					$value = $values;			
				break;
				default:
				
				break;
			}
			$return[$key] = $value;
			if (count($urls)>0){
				$return["userfile_urls"] = $urls;	
			}
		}

		return $return;
	}

	private static function save_photo($filepath, $value)
	{
		$_file = Array(
				'tmp_name' => $filepath,
				'size' => filesize($filepath),
				'name' => $value,
				'type' => mime_content_type($filepath),
			);
		return Uploads::save($_file);
	}

	public static function get_dictionary($config, $user_id, $massload_id)
	{
		$fields = $config["fields"];
		$dictionary 		= Array();
		$form_dictionary 	= Array();
		foreach ($fields as $field)
		{
			$type = $field["type"];
			$name = $field["name"];
			switch ($type) {
				case 'city':
					$form_dictionary[$type][0] = array("name"=>"Город");

					$cities = ORM::factory('City')->where("is_visible","=",1)->order_by("title")->find_all();
					foreach ($cities as $city) {
						$dictionary[$type."_".$city->title] = $city->id;
						$form_dictionary[$type][$city->title] = NULL;
						$user_conform = ORM::factory('User_Conformities')
	    							->where("user_id","=",$user_id)
	    							->where("massload","=",$massload_id)
	    							->where("type","=",$type)
	    							->where("value","=",$city->title)
	    							->find();
	    				if ($user_conform->loaded()){
	    						$dictionary[$type."_".$user_conform->conformity] = $city->id;
	    						$form_dictionary[$type][$city->title] = $user_conform->conformity;
	    				}
					}

					
				break;
				case 'dict':
					$attribute = ORM::factory('Attribute')->where("seo_name","=",$name)->find();
					$form_dictionary[$name][0] = array("name"=>$attribute->title);

					$elements = ORM::factory('Attribute_Element')->by_attribute_seoname($name)->order_by("title")->find_all();
					foreach ($elements as $element) {
						$dictionary[$name."_".$element->title] = $element->id;
						$form_dictionary[$name][$element->title] = NULL;

						$user_conform = ORM::factory('User_Conformities')
	    							->where("user_id","=",$user_id)
	    							->where("massload","=",$massload_id)
	    							->where("type","=",$name)
	    							->where("value","=",$element->title)
	    							->find();
	    				if ($user_conform->loaded()){
	    						$dictionary[$name."_".$user_conform->conformity] = $element->id;
	    						$form_dictionary[$name][$element->title] = $user_conform->conformity;
	    				}
					}
				break;	
				default:
					$form_dictionary[$name][0] = array("name"=>$fields[$name]["translate"]);
				break;			
			}
		}

		return array($dictionary, $form_dictionary);
	}
}