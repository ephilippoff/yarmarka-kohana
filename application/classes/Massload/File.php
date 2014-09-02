<?php defined('SYSPATH') or die('No direct script access.');

class Massload_File
{
	const DATAFILENAME = "load.csv";
	const ZIP = "zip";
	const CSVSEPARATOR = ",";

	private static $valid_extension = Array("csv", "zip", "xml", "xlsx", "xls");

	public function init($file, $user_id)
	{	
		$file = FileUtils::checkFile($file);

		@list($filename, $ext) = self::saveFile($file, $user_id);

		if (!FileUtils::checkValidExtension(FileUtils::getPath($filename), self::$valid_extension))
		{
			throw new Exception("Не правильный формат файла. Допустимые: ".join(", ",self::$valid_extension));
		};

		if ($ext == self::ZIP)
			@list($filepath, $imagepath) = self::unzipFile($filename);
		else 
			@list($filepath, $imagepath) = Array( FileUtils::getPath($filename), "" );

		return Array($filepath, $imagepath);
	}

	public function createMassload($path, $user_id, $category)
	{
		$massload = ORM::factory('Massload')
						->where('path', '=', $path)
						->where('user_id', '=', $user_id)
						->find();
		$massload->path  	= $path;
		$massload->user_id  = $user_id;
		$massload->category  = $category;
		$massload->save();

		return $massload->id;
	}

	private static function saveFile($file, $user_id){
		$filepath 	= Uploads::save_file($file);
		$ext 		= FileUtils::getExtension($file);

		if ($ext <> self::ZIP)
			$type = 1;
				else 
					$type = 2;

		$attachment = ORM::factory('User_Attachment');
		$attachment->filename 	= $filepath;
		$attachment->user_id 	= $user_id;
		$attachment->title 		= $file['name'];
		$attachment->type 		= $type;
		$attachment->save();
 
		return array($filepath, $ext);
	}

	private static function unzipFile($filename)
	{
		$path = FileUtils::getPath($filename);
		$pathtounzip =  str_replace(self::ZIP,'', $path).'/';
		exec('unzip -o '.$path.' -d '.$pathtounzip);

		return Array($pathtounzip.self::DATAFILENAME, $pathtounzip);
	}

	private static function openFile($pathtofile)
	{
		return fopen($pathtofile, "r");
	}

	private static function closeFile($file_instance)
	{
		return fclose($file_instance);
	}

	public static function forEachRow($config, $pathtofile, $callback)
	{
		$ext = File::ext_by_mime(mime_content_type($pathtofile));
		if ($ext == "xml"){
			Massload_FileXml::forEachRow($config, $pathtofile, $callback);
			return;
		} else 
		if ($ext == "xls" OR $ext == "xlsx"){
			Massload_FileXls::forEachRow($config, $pathtofile, $callback);
			return;
		}
		

		$file = self::openFile($pathtofile);

		$i = 0;
		while ( !feof($file) )
		{
			$row = fgetcsv($file, self::CSVSEPARATOR);
			//$row = self::to_assoc_object($row, $config);
			$row = self::clear_row($row);
			$return = $callback($row, $i);
			if ($return == 'break') break;
			if ($return == 'continue') continue;
			
			$i++;
		}
		self::closeFile($file);
	}

	public static function forRow($config, $pathtofile, $row_num, $callback)
	{
		$ext = File::ext_by_mime(mime_content_type($pathtofile));
		if ($ext == "xml"){
			Massload_FileXml::forRow($config, $pathtofile, $row_num, $callback);
			return;
		} else 
		if ($ext == "xls" OR $ext == "xlsx"){
			Massload_FileXls::forRow($config, $pathtofile, $row_num, $callback);
			return;
		}

		$file = self::openFile($pathtofile);

		for ($i = 0; $i<$row_num; $i++)
			fgetcsv($file, ',');


		$row = fgetcsv($file, self::CSVSEPARATOR);
		$row = self::to_assoc_object($row, $config);
		$row = self::clear_row($row);
		$return = $callback($row, $i);

		self::closeFile($file);
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

	public static function clear_row($row)
	{
		foreach ($row as $key=>$value)
			$row->{$key} = strip_tags($value);
		return $row;
	}

	private static function get_field_by_key($array, $key)
	{
		$values = array_values($array["fields"]); 
		return $values[$key];
	}
}