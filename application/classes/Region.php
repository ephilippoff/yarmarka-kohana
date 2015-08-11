<?php defined('SYSPATH') OR die('No direct script access.');

class Region {

	private static $_cache = array();

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
		if (isset(self::$_cache['current_domain']))
		{
			return self::$_cache['current_domain'];
		}

		$main_domain = Kohana::$config->load('common.main_domain');
		if ($city = self::get_current_city() AND $city->seo_name)
		{
			$current_domain = $city->seo_name.'.'.$main_domain;
		}
		else
		{
			$current_domain = $main_domain;
		}

		self::$_cache['current_domain'] = $current_domain;

		return $current_domain;
	}

	public static function get_cookie_domain()
	{
		return '.'.Kohana::$config->load('common.main_domain');
	}

	public static function get_domain_by_city($city_id = NULL)
	{
		if ($city_id)
		{
			$city = ORM::factory('City', intval($city_id));
		}

		if ($city->loaded())
		{
			if ($city->seo_name)
			{
				return $city->seo_name.'.'.Kohana::$config->load('common.main_domain');
			}
			elseif ($city->region->loaded() AND $city->region->seo_name 
				AND $city->region->seo_name == 73) // @fixit котсыль,т.к. кроме как для тюменской не созданы домены
			{
				return $city->region->seo_name.'.'.Kohana::$config->load('common.main_domain');
			}
		}

		return self::get_current_domain();
	}
}
