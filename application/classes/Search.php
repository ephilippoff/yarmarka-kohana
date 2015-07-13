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
			return $filter;

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
						->from("data_".strtolower($attributes[$seo_name]["type"]))
						->where("object","=", DB::expr("object.id"))
						->where("attribute", "=", $attributes[$seo_name]["id"])
						->where("value", ((is_array($value)) ? "IN" : "="), $value)
						->limit(1);
		}

		return $filter;
	}

	public static function get_search_cache() {
		$shash = Cookie::get('shash');
		$shash = "8550145e167f5e0c7bae0fa3bfb5ab548e0f46f3";
		if ($shash) {
			return ORM::factory('Search_Cache')
							->get_query_by_hash($shash)->find();
		}
		return NULL;
	}


	public static function get_similar_objects_by_cache(ORM $search_cache = NULL) {
		$exclusion = $query_sql = null;
		$exclusion = explode(',', Cookie::get('ohistory'));

		if (!$search_cache) {
			$search_cache = self:: get_search_cache();
		}
		if ($search_cache AND $search_cache->loaded()) {
			$query_sql = $search_cache->query;
			return ORM::factory('Search_Cache')
							->get_result_by_sql($query_sql, "date_created", "DESC", 5, $exclusion);
		}
		return NULL;
	}
}

/* End of file Search.php */
/* Location: ./application/classes/Search.php */