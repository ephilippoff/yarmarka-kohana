<?php defined('SYSPATH') or die('No direct script access.');

class FileUtils
{
	const FILE_PATH = '/uploads';
	const max_uploaded_image_size = 5242880;

	public function save(array $_file)
	{

		$this->checkFile($_file);
		
		$this->saveOriginal();

		return $this->path_filename;
	}

	private function checkFile( $file ) {

		if ( !isset($file) || $file["size"] < 1 ) {
			throw new Exception("Нулевой размер файла", 417);
		}

		if ( $file["size"] > self::max_uploaded_image_size ) {
			throw new Exception("Извините, файл слишком большой для загрузки",2);
		}

		$this->file = $file;

		$filename = $file["name"];
		$i = strrpos($filename, ".");
		if ( $i !== false ) {
			$ext = strtolower(substr($filename, $i));
		}

		$this->ext = $ext;
	}

	private function saveOriginal() {
		//Делаем уникальное имя файлу
		do {

			$filename = md5(uniqid(""));

			$tgtfile   = self::getPath( $filename . $this->ext);

			$folder1 = substr($filename, 0, 2);
			$folder2 = substr($filename, 2, 2);
			$folder3 = substr($filename, 4, 2);

		} while (file_exists($tgtfile) || $folder1 == "ad" || $folder2 == "ad" || $folder3 == "ad");

		//Записываем имя
		$this->path_filename = $filename . $this->ext;

		//Создаем директории
		mkdir(dirname($tgtfile), 0777, true);

		copy($this->file['tmp_name'], $tgtfile);
		if (file_exists($this->file["tmp_name"])) unlink($this->file["tmp_name"]);
	}

	public static function getExt($file) {
		$filename = $file["name"];
		$i = strrpos($filename, ".");
		if ( $i !== false ) {
			$ext = strtolower(substr($filename, $i));
		}
		return $ext;
	}

	public static function getPath($filename) {
		return '.' . self::getSitePath($filename);
	}

	/**
	 * Получить путь для ссылки до оригинальной картинки по имени файла
	 * @param $filename string
	 * @return string
	 */
	public static function getSitePath($filename) {
		$folder1 = substr($filename, 0, 2);
		$folder2 = substr($filename, 2, 2);
		$folder3 = substr($filename, 4, 2);

		return self::FILE_PATH . '/file/' . $folder1 . '/' . $folder2 . '/' . $folder3 . '/' . $filename;
	}

}