<?php defined('SYSPATH') or die('No direct script access.');

class Massload_File
{
	const DATAFILENAME = "load.csv";
	const ZIP = "zip";
	const CSVSEPARATOR = ",";

	private static $valid_extension = Array("csv", "zip");

	public function init($file, $user_id)
	{	
		$file = self::loadFile($file);

		@list($filename, $ext) = self::saveFile($file, $user_id);

		if ($ext == self::ZIP)
			@list($filepath, $imagepath) = self::unzipFile($filename);
		else 
			@list($filepath, $imagepath) = Array( FileUtils::getPath($filename), "" );

		return Array($filepath, $imagepath);
	}

	private static function loadFile($file)
	{
		FileUtils::checkFile($file);
		
		if (!FileUtils::checkValidExtension($file, self::$valid_extension))
		{
			throw new Exception("Не правильный формат файла. Допустимые: ".join(", ",self::$valid_extension));
		};
		return $file;
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

	public static function forEachRow($pathtofile, $callback)
	{
		$file = self::openFile($pathtofile);
		$i = 0;
		while ( !feof($file) )
		{

			$return = $callback(fgetcsv($file, self::CSVSEPARATOR), $i);
			if ($return == 'break') break;
			if ($return == 'continue') continue;
			
			$i++;
		}
		self::closeFile($file);
	}

	public static function forRow($pathtofile, $step, $iteration, $callback)
	{
		$file = self::openFile($pathtofile);

		for ($i = 0; $i<$iteration*$step; $i++)
			fgetcsv($file, ',');

		while ($i<$iteration*$step+$step)
		{
			$i++;

			$return = $callback(fgetcsv($file, self::CSVSEPARATOR), $i);
			if ($return == 'break') break;
			if ($return == 'continue') continue;
			
			
		}
		self::closeFile($file);
	}
}