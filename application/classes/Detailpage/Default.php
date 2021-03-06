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
		$info['city'] = ORM::factory('City', $object->city_id)->get_row_as_obj();
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

	public function get_seo_links()
	{

		try {
			$attributes = $this->_info['object']->compiled['attributes'];
		} catch(Exception $e) {
			return $this;
		}

		if (!$attributes) return $this;

		$names_attributes = array_keys($attributes);

		if (!count($names_attributes)) $names_attributes = array('0');

		$seo_attrbiutes = ORM::factory('Reference')
				->select('attribute.seo_name')
				->join('attribute')
					->on('reference.attribute','=','attribute.id')
				->where('attribute.seo_name','IN',$names_attributes)
				->where('reference.is_seo_used','=',1)
				->cached(Date::WEEK)
				->getprepared_all(array('seo_name'));

		$seo_attrbiutes = array_map(function($item){
			return $item->seo_name;
		}, $seo_attrbiutes);

		if (!count($seo_attrbiutes)) $seo_attrbiutes = array('0');

		$values = ORM::factory('Data_List')
			->select('attribute_element.title2','attribute_element.url')
			->join('attribute')
				->on('data_list.attribute','=','attribute.id')
			->join('attribute_element')
				->on('data_list.value','=','attribute_element.id')
			->where('attribute.seo_name','IN',$seo_attrbiutes)
			->where('object','=', $this->_info['object']->id)
			->getprepared_all();

		$info = $this->_info;
		
		$seo_links = array_map(function($item) use ($info){

			return array(
				'text' => sprintf('%s %s в %s', $info['category']->title, $item->title2, $info['city']->title),
				'url' => sprintf('/%s%s', $info['category']->url, ($item->url) ? '/'.$item->url:'' ),
				'title' => sprintf('Объявления о продаже %s %s в %s', $info['category']->sinonim, $item->title2, $info['city']->title),
			);
		}, $values);

		$info = array();

		$info['seo_links'] = $seo_links ;

		$this->_info = array_merge($this->_info, $info);

		return $this;
	}


	public function get_seo_across_links()
	{
		$object = $this->_orm_object;
		$domain = new Domain();
		$city = $domain->get_city();
		$city_id = $city->id;

		$linked = ORM::factory('Seo_PopularObject')
					->select('query')
					->join('seo_popular_query')
						->on('seo_popular_query.id','=','seo_popularobject.query_id')
					->where('object_id','=',$object->id)
					->getprepared_all();

		if (!count($linked)) {

			$popular_queries =  ORM::factory('Seo_Popular')
									->where('city_id','=', $city_id)
									->where('linked_to','<', 100)
									->order_by('linked_to','asc')
									->limit(3)
									->find_all();
			
			foreach ($popular_queries as $query) {
				
				ORM::factory('Seo_PopularObject')
					->values(array(
						"query_id" => $query->id,
						"object_id" => $object->id
					))->save();

				$sp = ORM::factory('Seo_Popular', $query->id);
				$sp->linked_to = $sp->linked_to + 1;
				$sp->save();

			}

			$linked = ORM::factory('Seo_PopularObject')
					->select('query')
					->join('seo_popular_query')
						->on('seo_popular_query.id','=','seo_popularobject.query_id')
					->where('object_id','=',$object->id)
					->getprepared_all();
		}

		$info = array();

		$info['seo_across_links'] = array_map(function($item) use ($city) {
			return array(
				"url" => "/?search=".$item->query,
				"text" => sprintf('Поиск объявлений "%s" в %s', $item->query, $city->sinonim),
				"title" => sprintf(' Объявления о продаже "%s" в %s', $item->query, $city->sinonim)
			);
		}, $linked);

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
		
		$info['similar_vip_search_result'] = $this->get_vip_similar_query($object, $domain);

		foreach ($info['similar_vip_search_result'] as $item) {
			$not_id = array();
			array_push($not_id, $item['id']);
		}

		$similar_search_query = Search::searchquery(
			array(
				'active' => true,
				'city_id' => array($domain->get_city()->id),
				"published" => TRUE,
				"photocard" => FALSE,
				'category_id' => $object->category,
				"not_id" => Cookie::get('ohistory') ? array_merge(explode(",", Cookie::get('ohistory')), array($object->id)) : array($object->id)
			),
			array("limit" => 10, "page" => 0)
		);

		// var_dump($similar_search_query); die;

		$info['similar_search_result'] = Search::getresult($similar_search_query->execute()->as_array());

		shuffle($info['similar_search_result']);

		$info['similar_search_result'] = array_merge($info['similar_vip_search_result'], $info['similar_search_result']);


		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

	public function get_vip_similar_query($object, $domain){
		$query = Search::searchquery(
			array(
				'active' => true,
				'city_id' => array($domain->get_city()->id),
				"published" => TRUE,
				'photocard' => true,
				'category_id' => $object->category,
				"not_id" => array($object->id)
			),
			array("limit" => 2, "page" => 0)
		);

		// var_dump($object->id); die;

		$info['similar_vip_search_result'] = Search::getresult($query->execute()->as_array());

		return $info['similar_vip_search_result'];
	}

}