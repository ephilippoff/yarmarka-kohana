<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Category 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Category extends ORM {

	protected $_table_name = 'category';

	public function get_url($region_id = NULL, $city_id = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$region = $region_id 
			? ORM::factory('Region', intval($region_id))
			: Region::get_current_region();

		$city = $city_id 
			? ORM::factory('City', intval($city_id))
			: Region::get_current_city();

		if ( ! $city AND ! $region)
		{
			$region = Region::get_default_region();
		}

		$geo = ($city AND $city->loaded()) ? $city->seo_name : $region->seo_name;

		return CI::site($geo.'/'.$this->seo_name);
	}
} // End Category Model
