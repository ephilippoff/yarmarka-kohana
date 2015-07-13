<?php defined('SYSPATH') OR die('No direct script access.');

class Object_Compiled
{

	public static function getImages($_images = array())
	{
		$result = array();

		if (!count($_images))
			return $result;
		

		$images = array_reverse($_images);

		if (count($images))
			$result["logo"] = Imageci::getSavePaths(array_shift($images));
		if (count($images))
			$result["main"] = Imageci::getSavePaths(array_shift($images));

		$result["other"] = array();
		if (count($images))
			foreach ($images as $image) {
				$result["other"][] = Imageci::getSavePaths($image);
			}
		return $result;
	}

	public static function getAttributes($attributes = array())
	{
		if (!$attributes)
			return;

		$result = array();
		foreach ($attributes as $attribute) {
			
			$_attribute = array();
			$_attribute["title"] = $attribute["_attribute"]["title"];
			$_attribute["seo_name"] = $attribute["_attribute"]["seo_name"];
			$type = $attribute["_attribute"]["type"];

			switch ($type) {
				case "list":
					$_attribute["value"] = $attribute["_element"]["title"];
				break;
				case "integer":
				case "numeric":
					$_attribute["value"] = $attribute["value_min"];
				break;
				default:
					$_attribute["value"] = $attribute["value"];
				break;
			}
			$result[] = $_attribute;
		}	

		return $result;
	}
}