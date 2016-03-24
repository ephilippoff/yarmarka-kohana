<?php defined('SYSPATH') OR die('No direct script access.');

class Object_Compiled
{

	public static function getImages($_images = array())
	{
		$result = array();

		if (!count($_images))
			return $result;
		

		$images = array_reverse($_images);

		if (count($images) AND $images["main_photo"])
			$result["main_photo"] = Imageci::getSavePaths($images["main_photo"]);

		$result["local_photo"] = array();
		if (count($images)) {
			foreach ($images["local_photo"] as $image) {
				$result["local_photo"][] = Imageci::getSavePaths($image);
			}
		}

		$result["remote_photo"] = array();
		if (count($images)) {
			foreach ($images["remote_photo"] as $image) {
				$result["remote_photo"][] = $image;
			}
		}

		$result["youtube_video"] = Arr::get($images, "youtube_video", NULL);

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
			if (array_key_exists($_attribute["seo_name"], $result)) {
				$result["_".$_attribute["seo_name"]] = $_attribute;
			} else {
				$result[$_attribute["seo_name"]] = $_attribute;
			}
			
		}	

		return $result;
	}
}