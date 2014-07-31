<?php defined('SYSPATH') or die('No direct script access.');

class FileUtils
{
	const FILE_PATH = '/uploads';
	const max_uploaded_size = 25242880;

	public function save(array $_file)
	{
		return self::saveOriginal( self::checkFile($_file) );
	}

	public static function checkFile( $file ) 
	{

		if ( !isset($file) || $file["size"] < 1 ) {
			throw new Exception("Нулевой размер файла", 417);
		}

		if ( $file["size"] > self::max_uploaded_size ) {
			throw new Exception("Извините, файл слишком большой для загрузки",2);
		}

		return $file;
	}

	public static function saveOriginal($file) 
	{
		$ext = self::getExtension($file);
		//Делаем уникальное имя файлу
		do {

			$filename = md5(uniqid(""));

			$filename_with_ext = $filename .".". $ext;

			$tgtfile   = self::getPath( $filename_with_ext );

			$folder1 = substr($filename, 0, 2);
			$folder2 = substr($filename, 2, 2);
			$folder3 = substr($filename, 4, 2);

		} while (file_exists($tgtfile) || $folder1 == "ad" || $folder2 == "ad" || $folder3 == "ad");

		//Создаем директории
		mkdir(dirname($tgtfile), 0777, true);

		copy($file['tmp_name'], $tgtfile);

		if (file_exists($file["tmp_name"])) unlink($file["tmp_name"]);

		return $filename_with_ext;
	}

	public static function getExtension($file) 
	{
		return File::ext_by_mime($file['type']);
	}

	public static function checkValidExtension($file, $valid_extensions = Array())
	{
		return (in_array( self::getExtension($file), $valid_extensions)) ? TRUE : FALSE;
	}

	public static function getPath($filename) {
		return '.' . self::getSitePath($filename);
	}

	/**
	 * Получить путь для ссылки до оригинальной картинки по имени файла
	 * @param $filename string
	 * @return string
	 */
	public static function getSitePath($filename) 
	{
		$folder1 = substr($filename, 0, 2);
		$folder2 = substr($filename, 2, 2);
		$folder3 = substr($filename, 4, 2);

		return self::FILE_PATH . '/file/' . $folder1 . '/' . $folder2 . '/' . $folder3 . '/' . $filename;
	}

}