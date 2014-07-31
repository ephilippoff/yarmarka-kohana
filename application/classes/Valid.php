<?php defined('SYSPATH') OR die('No direct script access.');

class Valid extends Kohana_Valid {
	/**
	 * Checks if a field is not 0
	 *
	 * @return  boolean
	 */
	public static function not_0($value)
	{
		return intval($value) !== 0;
	}

	public static function check_city_value($value, $dictionary)
	{
		if (array_key_exists("city_".$value, $dictionary)){
			$value = (int) $dictionary["city_".$value];//(int) ORM::factory('City')->by_title($value)->id;
		} else {
			$value = 0;
		}
		return intval($value) > 0;
	}

	public static function check_dictionary_value($value, $name, $dictionary)
	{
		if (array_key_exists($name."_".$value, $dictionary)){
			$value = (int) $dictionary[$name."_".$value];//ORM::factory('Attribute_Element')->by_value_and_attribute($value, $name)->find()->id;
		} else {
			$value = 0;
		}
		return intval($value) > 0;
	}

	public static function check_contact($number)
	{
		if (Valid::email($number))
		{
			$return = 5;
		} else 
		{
			$return = (int) ORM::factory('Contact_Type')->detect_contact_type_massload(Text::clear_phone_number($number));
		}
		if ($return == 1 OR $return == 2){
			if (strlen(Text::clear_phone_number($number)) <> 10)
				$return = 0;
		}
		return intval($return) > 0;
	}

	public static function check_photo($link, $path)
	{
		$type = NULL;
		if (filter_var($link, FILTER_VALIDATE_URL))
			$type = 'url';
		elseif (is_dir($path.$link."/"))
			$type = 'dir';
		elseif ( file_exists($path.$link) )
			$type = 'file';
		elseif ( $link == "0" OR $link == "" )
			$type = 'null';

		return isset($type);
	}

	
}
