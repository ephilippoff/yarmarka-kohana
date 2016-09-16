<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_City 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_City extends ORM {

	protected $_table_name = 'city';

	protected $_has_many = array(
		'users'	=> array(),
	);

	protected $_belongs_to = array(
		'region'	=> array('model' => 'Region', 'foreign_key' => 'region_id'),
		'location'	=> array(),
	);

	public function get_url()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return 'http://'.$this->seo_name.'.'.Kohana::$config->load('common.main_domain');
	}

	public function by_title($title)
	{
		return $this->where("title","=",$title)
					->where("is_visible",">",0)
					->find();
	}

	public function visible($value = true) {
		return $this->where('is_visible', '=', $value);
	}

	public function map($exclude_cities_ids = array()) {

		$key = "cities_for_menu:".serialize($exclude_cities_ids);

		if ( !$result = Cache::instance('memcache')->get($key) )  {

			$main_domain = ORM::factory('City',1)->get_row_as_obj();
			$cities = $this->visible(true)->getprepared_all();
			$main_cities = Kohana::$config->load('common.main_cities');

			$result = array();
			
			$isExcludeMain = in_array(1, $exclude_cities_ids);

			if (!$isExcludeMain) {
			    array_push($result, $main_domain);
			}

			foreach ($main_cities as $city) {

			    $find = array_filter($cities, function($city_search) use ($city){
			        return $city_search->seo_name === $city;
			    });

			    $result = array_merge($result, $find);
			}

			foreach ($cities as $city) {

			    if (!in_array($city->seo_name, $main_cities) AND $city->id <> 1) {
			        $result = array_merge($result, array( $city));
			    }

			}

			$result = array_filter($result, function($city) use ($exclude_cities_ids) {
		        return !in_array($city->id, $exclude_cities_ids);
		    });

			Cache::instance('memcache')->set($key, $result, Date::WEEK);

		} 

		return $result;
	}

} // End City Model
