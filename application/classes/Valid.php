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
		$value = (int) $dictionary["city_".$value];//(int) ORM::factory('City')->by_title($value)->id;
		return intval($value) > 0;
	}

	public static function check_dictionary_value($value, $name, $dictionary)
	{
		$value = (int) $dictionary[$name."_".$value];//ORM::factory('Attribute_Element')->by_value_and_attribute($value, $name)->find()->id;

		return intval($value) > 0;
	}

	public static function check_contact($value)
	{
		$value = (int) ORM::factory('Contact_Type')->detect_contact_type_massload(Text::clear_phone_number($value));
		return intval($value) > 0;
	}

	
}
