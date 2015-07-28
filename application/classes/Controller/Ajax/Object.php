<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Object extends Controller_Ajax {

	

	public function action_favourite() {
		if ( in_array($_SERVER['REQUEST_METHOD'], array("POST", "PUT")) ) {
			$this->action_favourite_post();
		} else {
			$this->action_favourite_get();
		}
	}

	public function action_favourite_post(){
		$id = (int) $this->request->param("id");
		if (!$id or $id <= 0) {
			$this->json["code"] = 400;
			return;
		}
		
		$this->json["result"] = ORM::factory('Favourite')->saveget_by_cookie($id);
		$this->json["favourites"] = ORM::factory('Favourite')->get_list_by_cookie();
	}

	public function action_favourite_get(){
		$this->json["result"] = ORM::factory('Favourite')
									->get_list_by_cookie();
	}


	public function action_global_search() {
		$text 		 = $this->request->query('text');
		$city_id 	 = (int) $this->request->query('city_id');
		$category_id = (int) $this->request->query('category_id');

		$region_id = 0;

		$this->json['objects'] = array();
		$this->json['pricerows'] = array();
		$this->json['objects_found'] = 0;
		$this->json['pricerows_found'] = 0;

		if (!empty($text))
		{
			$sphinx = new Sphinx();
			$result = $sphinx->search($text, $category_id, $city_id, FALSE, NULL, 0, 5);

			$objects = Sphinx::getObjects($result);
			$pricerows = Sphinx::getPricerows($result, $city_id);

			$objects = array_slice($objects,0,6);

			$objects = ORM::factory('Object')->info_by_ids(implode(",",$objects))->find_all();
			
			$resobjects = array();
			foreach ($objects as $object) {
				$o = new Obj($object->as_array());
				$o->category_url = ORM::factory('Category',$object->category_id)->get_url($region_id, $city_id, NULL);
				$resobjects[] = $o;
			}

			$this->json['objects'] = $resobjects;
			$this->json['pricerows'] = $pricerows;
			try {
				$this->json['objects_found'] = $result["objects"]["total_found"] ? $result["objects"]["total_found"] : 0;
				$this->json['pricerows_found'] = $result["pricerows"]["total_found"] ? $result["pricerows"]["total_found"] : 0;
			} catch(Exception $e){}
			
		}
	}
}