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

	public function get_crumbs()
	{
		$object = $this->_orm_object;
		$info = array();

		$info['crumbs'] = Search_Url::get_category_crubms($object->category);

		$attributes = @$this->_info['object']->compiled['attributes'];

		if ($attributes) {
			foreach ($attributes as $attribute) {
				
				$ae = ORM::factory('Attribute_Element')
						->join("attribute")
							->on("attribute.id","=","attribute_element.attribute")
						->where("attribute.seo_name","=",$attribute['seo_name'])
						->where("attribute_element.title","=",$attribute['value'])
						->cache(Date::WEEK)
						->find();

				$attribute['uri'] = $ae->url;
				$attribute['title'] = $attribute['value'];
				$attribute['query'] = '';
				array_push($info['crumbs'], $attribute);
			}
		}

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

}