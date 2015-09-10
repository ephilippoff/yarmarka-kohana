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
	
	public function rules()
	{
		return array(
			'title' => array(array('not_empty'),),
			'parent_id' => array(array('digit'), array('not_empty'),),
			'sinonim' => array(array('not_empty'),),
			'seo_name' => array(array('not_empty'),),
			'url' => array(array('not_empty'),),
		);
	}

	public function filters()
	{
		return array(
			'title' => array(array('trim'),),
			'is_ready' => array(array('intval'),),
			'weight' => array(array('intval'),),
			'template' => array(array('trim'),),
			'use_template' => array(array('intval'),),
			'is_main' => array(array('intval'),),
			'main_menu_icon' => array(array('trim'),),
			'sinonim' => array(array('trim'),),
			'seo_name' => array(array('trim'),),
			'description' => array(array('trim'),),
			'max_count_for_user' => array(array('intval'),),
			'max_count_for_contact' => array(array('intval'),),
			'is_main_for_seo' => array(array('intval'),),
			'title_auto_fill' => array(array('intval'),),
			'text_required' => array(array('intval'),),
			'nophoto' => array(array('intval'),),
			'novideo' => array(array('intval'),),
			'main_menu_image' => array(array('trim'),),
			'submenu_template' => array(array('trim'),),
			'caption' => array(array('trim'),),
			'text_name' => array(array('trim'),),
			'rule' => array(array('trim'),),
			'show_map' => array(array('intval'),),
			'address_required' => array(array('intval'),),
			'plan_name' => array(array('trim'),),
			'through_weight' => array(array('intval'),),
			'url' => array(array('trim'),),
		);
	}	

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
			? ORM::factory('Region', intval($region_id))->cached(Date::WEEK, array("city", "seo"))
			: Region::get_current_region();

		$city = $city_id 
			? ORM::factory('City', intval($city_id))->cached(Date::WEEK, array("city", "seo"))
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
				->cached(DATE::WEEK, array("category", "seo"))
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

	public function get_count_active_object_in_category($user, $object_id)
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
			$objects = $objects->where('id', '!=', $object_id);
		}

		return $objects->count_all();
	}

	public function get_count_childs($category_id)
 	{
 		return $this->where("parent_id","=",$category_id)->count_all(NULL, DATE::WEEK, array("category", "add"));
 	}

 	public function get_default_action($category_id)
 	{
 		$ac = ORM::factory('Action_Category')
						->where("category_id","=",$category_id)
						->where("is_default","IS NOT", NULL)
						->cached(DATE::WEEK, array("category", "add"))
						->find();
		if ($ac->action_id)
			return $ac->action_id;
		else 
			return null;
 	}
	
	//Получить рубрики первого уровня вложенности
	public function get_rubrics1l()
	{
		return ORM::factory('Category')
				->where('parent_id', '=', 1)
				->where('is_ready', '=', 1)
				->order_by('through_weight')
				->order_by('title');
	}	
	
	//Получить дочерние рубрики
	public function get_childs($parent_ids = array(), $with_subchilds = FALSE)
	{
		if ($with_subchilds) {

			$categories = ORM::factory('Category')
				->where('parent_id', 'in', $parent_ids)
				->where('is_ready', '=', 1)
				->order_by('weight')
				->order_by('title')
				->getprepared_all();
			$ids = array_map(function($value){
				return $value->id;
			}, (array) $categories);
			
			return $this->get_childs(array_merge($ids, $parent_ids));
		}
		return ORM::factory('Category')
				->where('parent_id', 'in', $parent_ids)
				->where('is_ready', '=', 1)
				->order_by('weight')
				->order_by('title');
	}
	
	//Взять баннеры категорий для региона/города
	function get_banners($city_id = 1, $states = array(1), $cached = true)
	{
		$data = FALSE;
		
		$city_id = (int)$city_id ? (int)$city_id : 1;		
		$states_str = implode(',', $states);

		if ($cached)
			$data = Cache::instance()->get("getBannersForCategories:$city_id");	
		
		if (!$data)
		{		
			$data = ORM::factory('Category_Banners')
						->where(DB::expr($city_id), '=', DB::expr('ANY(cities)'))
						->where('date_expired', '>=', DB::expr('CURRENT_DATE'))
						->where('state', 'in', DB::expr('('.$states_str.')'))
						->getprepared_all();
			
			if ($cached)
				Cache::instance()->set("getBannersForCategories:{$city_id}", $data, 60*60);				
		}
		
		return $data;		
	}

	function get_limited()
	{
		return $this->where("max_count_for_user",">",0);
	}

	function get_individual_limited($user_id, $category_id = 0)
	{
		$result = array();
		$categories = $this->get_limited();

		if ($category_id)
			$categories = $categories->where("id","=",$category_id);

		$categories = $categories->find_all();
		foreach ($categories as $_category) {
			$limit = ORM::factory('User_Settings')
								->get_by_group_and_name($user_id, "userinfo", "limit_".$_category->id)
								->find()->value;
			$category = $_category->as_array("id","title");
			if ((int) $limit > 0) {
				$category["individual_limit"] = $limit;
				$result[] = $category;
			}

		}

		return $result;
	}

	function get_categories_extend($params = array("with_child" => TRUE, "with_ads" => TRUE, "city_id" => NULL))
	{
		$params = new Obj($params);
		$categories1l_ids = $parents_ids = $result = array();

		$categories1l = $this->get_rubrics1l()
								//->cached(60*24)
								->getprepared_all();
		$result["main"] = $categories1l;

		//получаем массив id'шников
		foreach ($categories1l as $category) 
			$categories1l_ids[] = $category->id;
		$result["main_ids"] = $categories1l_ids;
		
		//получить баннеры рубрик по региону/городу для показа в меню
		$states = array(1);
		$cached = TRUE;		
			
		if (Auth::instance()->get_user() and in_array((int)Auth::instance()->get_user()->role, array(1, 9)))
		{
			$states = array(1, 2);
			$cached = FALSE;
		}

		if ($params->with_child) {
			//получаем рубрики второго уровня
			$categories2l = ORM::factory('Category')
							->get_childs($categories1l_ids)
							//->cached(60*24)
							->getprepared_all();
			$result["childs"] = $categories2l;
		}

		if ($params->with_ads) {
			$city_id = ($params->city_id) ? $params->city_id : 1;
			$banners = $this->get_banners($city_id, $states, $cached);

			$categories_banners = array_map(function($value) {
				return $value->category_id;
			}, $banners);
			if (count($banners) > 0) {
				$result["banners"] = array_combine($categories_banners, $banners);
			} else {
				$result["banners"] = array();
			}
		}

		return $result;
	}

	function get_parent($category_id)
	{
		$result = array();

		$category = ORM::factory('Category')->where("id","=",$category_id)->find();

		if ($category->loaded()) 
		{
			$result[] = $category->get_row_as_obj();
			
			if ($category->parent_id) 
			{
				$result = array_merge($result, self::get_parent($category->parent_id));
			}
		}

		return $result;
	}
}

/* End of file Category.php */
/* Location: ./application/classes/Model/Category.php */