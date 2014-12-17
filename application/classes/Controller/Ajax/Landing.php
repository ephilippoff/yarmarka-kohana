<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Landing extends Controller_Ajax {


	public function action_price_navigate_object(){

		$text 			= $this->request->post('text');
		$object_id 		= $this->request->post('object_id');
		$page 			= $this->request->post('page');
		$hierarchy_filter_id 	= $this->request->post('hierarchy_filter_id');
		$hierarchy_attribute_id 	= $this->request->post('hierarchy_attribute_id');


		$post_keys = array_keys($this->request->post());
		$_attributes = preg_grep("/^attribute_/", $post_keys);
		$attributes = array();
		foreach ($_attributes as $_attribute) {
			$attribute = explode("_", $_attribute);
			$value = $this->request->post($_attribute);
			if ($value AND $value<>"")
				$attributes[$attribute[1]] = $value;
		}

		if ($hierarchy_filter_id AND $hierarchy_attribute_id)
			$attributes[$hierarchy_attribute_id] = $hierarchy_filter_id;

		$limit = 50;
		$offset = $page*$limit;

		$pricerows = array();
		$op	= ORM::factory('Object_Priceload')
								->where("object_id","=",$object_id)
								->find();

		if (!$op->loaded()){
			$this->json["code"] = "404";
			return;
		}
		$priceload = $op->priceload_obj;

		if ($text <> "")
		{
			$sphinx = new Sphinx();
			$result = $sphinx->search($text, NULL, NULL, FALSE, $object_id, $offset, $limit);
			$pricerows = Sphinx::getPricerows($result, 0);
			$pricerows = Landing_Object::clearSphinxValues($priceload, $pricerows);
			$count_pricerows = $result["pricerows"]["total"];
		} else {			
			if ($op->loaded()){
				$pricerows = Landing_Object::getPricelist( $priceload,  $op->priceload_id , $attributes, $limit, $offset);
				$count_pricerows = Landing_Object::getPricelistCount($op->priceload_id, $attributes, $limit, $offset);
			}

		}

		$this->json["data"] = $pricerows;
		$this->json["count_rows"] = count($pricerows);
		$this->json["count_all_rows"] = $count_pricerows;

		//запрос вложенных категорий иерархического меню
		
		$hierarchy_filter_id 	= $this->request->post('hierarchy_filter_id');
		$childs = array();
		if ($hierarchy_filter_id)
		{
			$hierarchy_attribute_id 	= $this->request->post('hierarchy_attribute_id');
			
			$filter_childs = ORM::factory('Priceload_Filter')
									->where("priceload_attribute_id","=",$hierarchy_attribute_id)
									->where("parent_id","=", $hierarchy_filter_id)
									->cached(Date::DAY)
									->find_all();

			foreach($filter_childs as $filter) {
					$childs[] = array(
							"id" =>$filter->id,
							"attribute_id" =>$filter->priceload_attribute_id,
							"title" => $filter->title,
							"count" => $filter->count
						);		
			}
		}

		$this->json["filter_childs"] = $childs;
		
	}

}