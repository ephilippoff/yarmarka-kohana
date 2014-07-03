<?php defined('SYSPATH') or die('No direct script access.');

class Massload 
{
	const  CSVSEPARATOR = ',';

	public $inputfile;
	public $fileForLoop;
	public $ext;
	public $filename;
	public $pathtofile;
	public $pathtoimage;
	public $config;
	public $user;

	public function __construct($inputfile, $category_id, $user) {
		$this->inputfile 	 	= $inputfile;
		$this->category_id 		= $category_id;
		$this->user 			= $user;
	}

	function save_input_file()
	{
		$inputfile = $this->inputfile;
		$filename = Uploads::save_file($inputfile);

		$ext = FileUtils::getExt($inputfile);

		if ($ext <>'.zip')
			$type = 1;
				else 
					$type = 2;

		$this->ext = $ext;
		$this->filename = $filename;

		$attachment = ORM::factory('User_Attachment');
		$attachment->filename 	= $filename;
		$attachment->user_id 	= $this->user->id;
		$attachment->title 		= $inputfile['name'];
		$attachment->type 		= $type;
		$attachment->save();

		return $this;
	}

	function get_input_file_path_by_ext()
	{
		$ext 		= $this->ext;
		$filename 	= $this->filename;

		if ($ext =='.zip')
		{
			$path = FileUtils::getPath($filename);
			$pathtounzip =  str_replace('.zip','', $path).'/';
			exec('unzip -o '.$path.' -d '.$pathtounzip);

			$this->pathtofile	= $pathtounzip.'load.csv';
			$this->pathtoimage 	= $pathtounzip;
		} else {
			$this->pathtofile 	= FileUtils::getPath($filename);
			$this->pathtoimage	= "";
		}

		return $this;
	}

	function file_open()
	{
		$this->fileForLoop = fopen($this->pathtofile, "r");
		return $this;
	}

	function file_close()
	{
		fclose($this->fileForLoop);
		return $this;
	}

	function get_config()
	{
		$category = ORM::factory('Category',$this->category_id);
		$this->config  = Kohana::$config->load('massload/bycategory.'.$category->seo_name);
		return $this;
	}

	function check($field, $value, $str_pos)
	{
		$error = NULL;

		if ( ! $this->check_required($field, $value)  )
			$error = $this->log_error( $str_pos, $field, $value, "Не заполнено обязательное поле");

		if ( ! $this->check_exist_values($field, $value)  )
			$error = $this->log_error( $str_pos, $field, $value, "Значение справочника не существует");

		if ($field["name"] == 'contact_0_value' OR $field["name"] == 'contact_1_value')
			if ( $value <> ""  AND $this->check_contact_type($value) == 0)
				$error = $this->log_error($str_pos, $field, $value, "Контакт имеет неизвестный формат");

		return $error;
	}

	function check_exist_values($field, $value)
	{
		$return = TRUE;
		
		if ($field["type"] == 'city')
			$return = ORM::factory('City')->by_title($value)->id;
		elseif ($field["type"] == 'dict')
			$return = ORM::factory('Attribute_Element')->by_value_and_attribute($value, $field['name'])->id;

		return $return;
	}

	function check_required($field, $value)
	{
		if ($field['required'] AND $value == '') 
			return FALSE; 
				else return TRUE;
	}

	function check_contact_type($value)
	{
		return ORM::factory('Contact_Type')->detect_contact_type_massload(Text::clear_phone_number($value));
	}

	function to_post_format($params, $config)
	{
		$category_id = $this->category_id;
		$pathtoimage = $this->pathtoimage;

		$return = Array();
		foreach($params as $key=>$value)
		{
			$type = $config[$key]['type'];
			switch ($type) {
				case 'city':
					$key = "city_id";
					$value = ORM::factory('City')->by_title($value)->id;				
				break;
				case 'dict':
					$value = ORM::factory('Attribute_Element')->by_value_and_attribute($value, $key)->id;
					$key = "param_".ORM::factory('Reference')->by_category_and_attribute($category_id, $key);					
				break;
				case 'integer':
					$key = "param_".ORM::factory('Reference')->by_category_and_attribute($category_id, $key);					
				break;
				case 'photo':			
					$filename = $pathtoimage.$value;
					
					if (is_dir($filename."/"))
						$type = 'dir';
					elseif ( file_exists($filename) )
						$type = 'file';

					switch ($type) {
						case 'file':						
							$key = "userfile";					
							$value = Array( $this->save_photo($filename, $value));
						break;
						
						case 'dir':
							$files = glob($filename.'/*.{jpg,png,gif,bmp}', GLOB_BRACE);
							$key = "userfile";
							$value = Array();
							foreach($files as $file) {								
								$value[] = $this->save_photo($file, $file);
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

	function save_photo($filename, $value)
	{
		$file = fopen($filename, "r");
		$_file = Array(
				'tmp_name' => $filename,
				'size' => filesize($filename),
				'name' => $value,
				'type' => mime_content_type($filename),
			);
		return Uploads::save($_file);
	}

	function log_error($str_pos, $field, $value, $comment)
	{
		$error = array(
				'str_pos'=> $str_pos, 
				'field'	 =>$field['translate'], 
				'value'	 =>$value,
				'comment'=>$comment,
				);

		return $error;
	}

	public static function to_object($array)
	{
		return $this;
	}

	public static function get_by_key($array, $key)
	{
		$values = array_values($array); 
		return $values[$key];
	}
}
