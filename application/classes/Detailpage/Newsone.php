<?php defined('SYSPATH') or die('No direct script access.');

class Detailpage_Newsone extends Detailpage_Default
{
	protected $search_info = NULL;

	public function __construct($object)
	{
		parent::__construct($object);
	}

	public function get_last_news($city_id =  NULL)
	{
		$object = $this->_orm_object;

		$info = array();
		$info['months'] = Date::get_months_names();

		$category = ORM::factory('Category')
						->where("seo_name","=", "novosti")->find();

		if (!$category->loaded()) {
			return $this;
		}
	
		$search_query = Search::searchquery(
			array(
				"expiration" => TRUE,
				"active" => TRUE,
				"published" =>TRUE,
				"city_id" => $city_id,
				"category_seo_name" => "novosti",
				"not_id" => array($object->id)
			),
			array("limit" => 15, "page" => 1)
		);
		$info['lastnews'] = Search::getresult($search_query->execute()->as_array());

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

}