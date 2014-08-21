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

	protected $_has_many = array(
		'sub_categories' => array('model' => 'Category', 'foreign_key' => 'parent_id'),
		'business_types' => array('model' => 'Business_Type', 'through' => 'category_business'),
	);

	protected $_belongs_to = array(
		'category' => array('local_key' => 'parent_id'),
	);

	public function get_url($region_id = NULL, $city_id = NULL, $action_id = NULL)
	{
		return CI::site($this->get_seo_name($region_id, $city_id, $action_id));
	}

	public function get_seo_name($region_id = NULL, $city_id = NULL, $action_id = NULL)
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

		return $geo.'/'.$this->get_seo_without_geo($action_id);
	}

	public function get_seo_without_geo($action_id = NULL)
	{
		$category_seo_names = array();
		$parent_id = $this->parent_id;
		while ($parent_id != 1 AND $this->id != 1)
		{
			$category = ORM::factory('Category')
				->where('id', '=', $parent_id)
				->cached()
				->find();
			if ($category->seo_name)
			{
				$category_seo_names[0] = $category->seo_name;
			}
			$parent_id = $category->parent_id;
		}

		$category_seo_names[] = $this->seo_name;

		if ($action_id)
		{
			$action = ORM::factory('Action', intval($action_id));
			if ($action->loaded() AND $action->seo_name)
			{
				$category_seo_names[] = $action->seo_name;
			}
		}

		return join('/', $category_seo_names);
	}

	public function get_url_with_action($action_id)
	{
		return $this->get_url(NULL, NULL, $action_id);
	}

	public function get_small_icon()
	{
		return URL::site('images/min_'.$this->main_menu_icon);
	}

	public function get_icon()
	{
		return URL::site('images/'.$this->main_menu_icon);
	}

	public function check_max_user_objects($user, $object_id)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if ( ! is_object($user))
		{
			$user = ORM::factory('User', $user);
		}

		$objects = $user->objects->where('category', '=', $this->id)->where('is_published', '=', 1)->where('active', '=', 1);
		if ($object_id)
		{
			$objects->where('id', '!=', $object_id);
		}

		if ($user->org_type == 1 AND $this->max_count_for_user AND $objects->count_all() >= $this->max_count_for_user)
		{
			return FALSE;
		}

		return TRUE;
	}

	public function get_count_childs($category_id)
 	{
 		return $this->where("parent_id","=",$category_id)->count_all();
 	}

 	public function get_default_action($category_id)
 	{
 		$ac = ORM::factory('Action_Category')
						->where("category_id","=",$category_id)
						->where("is_default","IS NOT", NULL)
						->find();
		if ($ac->action_id)
			return $ac->action_id;
		else 
			return null;
 	}
}

/* End of file Category.php */
/* Location: ./application/classes/Model/Category.php */