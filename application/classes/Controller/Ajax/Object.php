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

}