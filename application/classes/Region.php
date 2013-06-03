<?php defined('SYSPATH') OR die('No direct script access.');

class Region {

	public static function get_current_region()
	{
		$region = ORM::factory('Region', intval(Arr::get($_COOKIE, 'location_region_id')));
		
		return $region->loaded() ? $region : FALSE;
	}

	public static function get_current_city()
	{
		$city = ORM::factory('City', intval(Arr::get($_COOKIE, 'location_city_id')));
		
		return $city->loaded() ? $city : FALSE;
	}

	public static function get_default_region()
	{
		return ORM::factory('Region', Kohana::$config->load('common.default_region_id'));
	}

	public static function get_current_domain()
	{
		$main_domain = Kohana::$config->load('common.main_domain');
		if ($city = self::get_current_city() AND $city->seo_name)
		{
			return $city->seo_name.'.'.$main_domain;
		}
		else
		{
			return $main_domain;
		}
	}
}
