<?php defined('SYSPATH') or die('No direct script access.');

class Search {

	public static function get_url_to_main_category($city_id = NULL)
	{
		$city_id = is_null($city_id) ? Arr::get($_COOKIE, 'location_city_id') : NULL;
		$region_id = Arr::get($_COOKIE, 'location_region_id', Kohana::$config->load('common.default_region_id'));

		$geo = ORM::factory('City', $city_id);
		if ( ! $geo->loaded())
		{
			$geo = ORM::factory('Region', $region_id);
		}

		$url = array();
		if ( ! $geo->loaded())
		{
			$url[] = 'search';
		}
		else
		{
			$url[] = $geo->seo_name;
			$category = ORM::factory('Category', 1);
			$url[] = $category->seo_name;
		}

		return join('/', $url);
	}
}

/* End of file Search.php */
/* Location: ./application/classes/Search.php */