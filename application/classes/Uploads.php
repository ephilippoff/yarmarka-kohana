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

	public static function get_optimized_file_sizes($filename, $size, $optimized_for)
	{
		$filepath = $_SERVER['DOCUMENT_ROOT'].self::get_file_path($filename, $size);
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}
		list($width, $height) = getimagesize($filepath);
		list($opt_width, $opt_height) = explode('x', $optimized_for);

		if ($width > $height)
		{
			$new_width = $opt_width;
			$new_height = round($height*($new_width/$width));
		}
		else
		{
			$new_height = $opt_height;
			$new_width = round($width*($new_height/$height));
		}

		return array($new_width, $new_height);
	}

	public static function delete($filename)
	{
		$image = new Image();
		return $image->deleteImage($filename);
	}

	public static function save(array $file, $original_resize = array())
	{
				error_reporting(-1);
		$image = new Image();
		if ($original_resize)
		{
			$image->set_original_resize($original_resize);
		}
		return $image->makeThumbnail($file);
	}
}
