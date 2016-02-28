<?php defined('SYSPATH') or die('No direct script access.');

class Detailpage_Default
{
	protected $_info = NULL;
	protected $_orm_object = NULL;

	public function __construct(ORM $object)
	{
		$this->_info = array();
		$this->_orm_object = $object;

		$this->_info = array_merge($this->_info, $this->get_detail_info());
		$this->_info = array_merge($this->_info, $this->get_user());
	}

	private function get_detail_info() {
		$object = $this->_orm_object;
		$info = array();

		$info['object'] = $object->get_row_as_obj();
		$info['category'] = ORM::factory('Category', $object->category)->get_row_as_obj();
		$info['object']->compiled =  Search::getresultrow((array) $info['object']);

		return $info;
	}

	public function get()
	{
		return new Obj($this->_info);
	}

	public function get_user()
	{
		$object = $this->_orm_object;
		$info = array();
		$user        = Auth::instance()->get_user();
		$info['user'] = ($user) ? $user->get_row_as_obj() : NULL;
		return $info;
	}
	
	public function get_crumbs()
	{
		$object = $this->_orm_object;
		$info = array();

		$info['crumbs'] = Search_Url::get_category_crubms($object->category);

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

	public function get_messages()
	{
		$object = $this->_orm_object;
		$info = array();

		$info['messages'] = ORM::factory('User_Messages')
							->get_messages($object->id)
							->getprepared_all();

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

	public function get_similar() {
		$object = $this->_orm_object;
		$info = array();

		$domain = new Domain();
		$object_location = ORM::factory('Location', $object->location_id);
		$object_location_value = $object_location->loaded() ? trim($object_location->city . ' ' . $object_location->address) : NULL;
		$similar_search_query = Search::searchquery(
			array(
				//"hash" => Cookie::get('search_hash'),
				'active' => true,
				//'city_id' => array($domain->get_city()->id),
				'location' => $object_location_value,
				'expiration' => true,
				'expired' => true,
				'is_published' => true,
				'category_id' => $object->category,
				"not_id" => Cookie::get('ohistory') ? 
									array_merge(explode(",", Cookie::get('ohistory')), array($object->id)) 
										: array($object->id)
			),
			array("limit" => 10, "page" => 0)
		);
		$info['similar_search_result'] = Search::getresult($similar_search_query->execute()->as_array());

		$info['similar_coords'] = array_map(function($item){
			return array(
				"id" => $item["id"],
				"title" => $item["title"],
				"price" => $item["price"],
				"photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
				"coords" => array(@$item["compiled"]["lat"], @$item["compiled"]["lon"])
			);
		}, $info['similar_search_result']);

		$info['objects_for_map'] = json_encode($info['similar_coords']);

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

}