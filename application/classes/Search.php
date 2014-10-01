<?php defined('SYSPATH') or die('No direct script access.');

class Search {

	public static function get_url_to_main_category($city_id = NULL)
	{
		$city_id = is_null($city_id) ? Arr::get($_COOKIE, 'location_city_id') : $city_id;
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

	public static function get_filters_by_params(array $params)
	{
		$filter = array();
		if (count($params) <= 0)
			return;

		$attributes = array();
		$_attributes = ORM::factory('Attribute')
				->where("seo_name", "IN", array_keys($params))
				->find_all();
		foreach ($_attributes as $attribute)
		{
			$attributes[$attribute->seo_name] = array(
									"id"   => $attribute->id,
									"type" => $attribute->type
								);
		}

		foreach ($params as $seo_name => $value) {

			$filter[] = DB::select("id")
						->from("data_".$attributes[$seo_name]["type"])
						->where("object","=", DB::expr("object.id"))
						->where("attribute", "=", $attributes[$seo_name]["id"])
						->where("value", ((is_array($value)) ? "IN" : "="), $value)
						->limit(1);
		}

		return $filter;
	}
}

/* End of file Search.php */
/* Location: ./application/classes/Search.php */