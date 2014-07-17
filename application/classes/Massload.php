<?php defined('SYSPATH') or die('No direct script access.');

class Massload 
{
	const MAX_COUNT_ERRORS = 10;

	public function checkFile($file, $category, $user_id)
	{
		$errors = Array();

		$f = new Massload_File();

		@list($filepath, $imagepath) = $f->init($file, $user_id);

		$config = self::get_config($category);
		@list($dictionary, $form_dictionary) = self::get_dictionary($config, $user_id, $config["category"]);

		$count = 0;

		$f->forEachRow($filepath, function($row, $i) use ($imagepath, &$errors, $config, &$count, $dictionary){

			$row = Massload::to_assoc_object($row, $config);

			if ($row->count() <> count($config["fields"]) )
			{
				$errors[] = '(Ошибка стр. '.$i.') Количество полей в строке не соответсвует требованиям для загрузки';
				return "continue";
			}

			$validation = Massload::init_validation($row, $i, $config, $dictionary);

			if ( ! $validation->check())
				$errors = array_merge($errors, array_values($validation->errors('validation/massload'))) ;

			$count++;

			if ($count>Massload::MAX_COUNT_ERRORS)
			{
				$errors[] = '(Ошибка) Найдено более '.Massload::MAX_COUNT_ERRORS.' ошибок. Проверка файла остановлена.';
				return "break";
			}

		});

		return Array($filepath, $imagepath, $errors, $count);
	}

	public function saveStrings($pathtofile, $pathtoimage, $category, $step, $iteration, $user_id)
	{
		$f = new Massload_File();

		$objects = Array();
		$config = self::get_config($category);
		@list($dictionary, $form_dictionary) = self::get_dictionary($config, $user_id, $config["category"]);
		$category_id = $config["id"];

		$f->forRow($pathtofile, $step, $iteration, function($row, $i) use ($pathtoimage, $config, $category_id, &$objects, $dictionary){

			$row = Massload::to_assoc_object($row, $config);

			if ($row->count() <> count($config["fields"]) )
			{
				return "continue";
			}

			$validation = Massload::init_validation($row, $i, $config, $dictionary);
			if (!$validation->check()) return "continue";	
			

			$record = Array();

			foreach ($row as $name=>$value) 
			{				
				if ($value <> "")
					$record[$name] = $value;				
			}	

			$record = Massload::to_post_format($record, $category_id, $pathtoimage, $config, $dictionary);

			$record = array_merge($record, $config["autofill"]);

			$objects[] = Object::PlacementAds_ByMassLoad($record);

		});

		return $objects;
	}

	private static function get_config($category)
	{
		return Kohana::$config->load('massload/bycategory.'.$category);
	}

	public static function to_assoc_object($row, $config)
	{
		$return = new Obj();
		foreach((array) $row as $key=>$value)
		{	$config_field = self::get_field_by_key($config, $key);
			$return->{$config_field["name"]} = $value;
		}
		return $return;
	}

	private static function get_field_by_key($array, $key)
	{
		$values = array_values($array["fields"]); 
		return $values[$key];
	}

	public static function init_validation($row, $i, $config, $dictionary)
	{
		$validation = Validation::factory((array) $row);

		$rules = Array();
		foreach ($row as $key=>$value)
		{
			$config_key = new Obj($config["fields"][$key]);

			$valid_info 		= array(':value', $dictionary, $config_key->translate, $i, $value);
			$valid_info_dict 	= array(':value', $config_key->name, $dictionary, $config_key->translate, $i, $value);

			if ($config_key->required) 
				$validation->rule($key, 'not_empty', $valid_info);

			if ($config_key->type == "city")
				$validation->rule($key, 'check_city_value', $valid_info);

			if ($config_key->type == "dict")
				$validation->rule($key, 'check_dictionary_value', $valid_info_dict);

			if ($config_key->type == "contact")
				$validation->rule($key, 'check_contact', $valid_info);

			if ($config_key->type == "integer")
			{
				$validation->rule($key, 'not_0', $valid_info);
				$validation->rule($key, 'digit', $valid_info);
			}

			if ($config_key->type == "numeric")
				$validation->rule($key, 'numeric', $valid_info);

		}

		return $validation;	
	}

	public static function to_post_format($record_fields, $category_id, $pathtoimage, $config, $dictionary)
	{
		//echo var_dump($dictionary);
		$return = Array();
		foreach($record_fields as $key=>$value)
		{
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
					$filename = $pathtoimage.$value;
					if (filter_var($filename, FILTER_VALIDATE_URL))
						$type = 'url';
					elseif (is_dir($filename."/"))
						$type = 'dir';
					elseif ( file_exists($filename) )
						$type = 'file';

					switch ($type) {
						case 'file':						
							$key = "userfile";					
							$value = Array( self::save_photo($filename, $value));
						break;
						
						case 'dir':
							$files = glob($filename.'/*.{jpg,png,gif,bmp}', GLOB_BRACE);
							$key = "userfile";
							$value = Array();
							foreach($files as $file) {	
								try {							
									$value[] = self::save_photo($file, $file);
								} catch (Exception $e) {}
							}
						break;

						case 'url':		
							$tmp = tempnam("/tmp", "imgurl");
							if (copy($value, $tmp))
							{				
								$key = "userfile";					
								$value = Array( self::save_photo($tmp, $tmp));
							}
						break;
					}

					
				break;

				
				default:
				# code...
				break;
			}
			$return[$key] = $value;
		}

		return $return;
	}

	private static function save_photo($filepath, $value)
	{
		$file = fopen($filepath, "r");
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
			}
		}

		return array($dictionary, $form_dictionary);
	}
}