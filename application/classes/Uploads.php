<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Facade for Image library
 * 
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Uploads
{
	public static function get_file_path($filename, $type = NULL)
	{
		if ($type)
		{
			$path = Image::getThumbnailPath($filename, $type);
		}
		else
		{
			$path = Image::getOriginalSitePath($filename);
		}

		return trim(trim($path), '.');
	}

	public static function delete($filename)
	{
		$image = new Image();
		return $image->deleteImage($filename);
	}

	public static function save(array $file)
	{
		$image = new Image();
		return $image->makeThumbnail($file);
	}
}
