<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Detail extends Controller_Template {
	
	public function before()
	{
		parent::before();

		$this->performance = Performance::factory(Acl::check('profiler'));

		$this->performance->add("Detail","start");

		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->domain = new Domain();
		if ($proper_domain = $this->domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
			return;
		}

		$is_old = $this->request->param("is_old");
		if ($is_old) {
			HTTP::redirect("detail/".$this->request->param("object_id").".html", 301);
			return;
		}

		$this->acl = new Acl("object");

		//TODO set header Last-Modified
		//$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', mysql_to_unix($last_modified)).' GMT');
	}

	public function action_index() {
		$start = microtime(true);
		$object = $this->request->param("object");
		$url = $this->request->param("url");

		if ($url <> $this->request->get_full_url()) {
			HTTP::redirect($url, 301);
		}

		if ($object->active == 0) {
		   throw new HTTP_Exception_404;
		   return;
		}

		$twig = Twig::factory('detail/index');
		$twig->domain      = $this->domain;
		$twig->city        = $this->domain->get_city();

		$this->performance->add("Detail","info");
		$detail_info = Detailpage::factory("Default", $object)
						->get_messages()
						->get_similar()
						->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}

		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites

		$this->performance->add("Detail","end");
		$twig->php_time = $this->performance->getProfilerStat();
		$this->response->body($twig);
	}

	public function action_type89() {

	}

	public function action_type201() {
		$start = microtime(true);
		$object = $this->request->param("object");
		$url = $this->request->param("url");

		if ($url <> $this->request->get_full_url()) {
			HTTP::redirect($url, 301);
		}

		if ($object->active == 0) {
		   throw new HTTP_Exception_404;
		   return;
		}

		$twig = Twig::factory('detail/kupon/index');
		$twig->domain      = $this->domain;
		$twig->city        = $this->domain->get_city();

		$detail_info = Detailpage::factory("Kupon", $object)->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		
		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites

		foreach ((array) $detail_info as $key => $item) {			
			$twig->{$key} = $item;
		}

		// //декодируем json-атрибут price-params
		// $price_params = json_decode($twig->object->compiled['attributes']['price-params']['value']);		
		// if ($price_params and json_last_error() == JSON_ERROR_NONE)
		// 	$twig->price_params_decoded = $price_params;
		// else
		// 	$twig->price_params_decoded = array();
		
		$twig->request_uri = $_SERVER['REQUEST_URI'];
		
		$this->performance->add("Detail","end");
		$twig->php_time = $this->performance->getProfilerStat();
		$this->response->body($twig);
	}

	public function after()
	{
		parent::after();
		$object = $this->request->param("object");
		Cookie::save_toobject_history($object->id);

		$visits = Cachestat::factory($object->id."object_visit_counter")->fetch();
		$visits = (!$visits) ? 0 : $visits;
		$visits = $visits + 1;
		Cachestat::factory($object->id."object_visit_counter")
					->add(0, $visits);

		Cachestat::factory("objects_in_visit_counter")
					->add($object->id, $object->id);
	}
} // End Detail
