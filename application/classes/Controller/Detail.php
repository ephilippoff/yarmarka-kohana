<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Detail extends Controller_Template {
	
	public function before()
	{
		parent::before();

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


		$detail_info = Detailpage::factory("Default", $object)
						->get_messages()
						->get_similar()
						->get_crumbs()
						->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}

		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites

		$user = Auth::instance()->get_user();
		$twig->isAdmin = $user && $user->role == 1;
		if ($twig->object->is_published == 0 && $twig->isAdmin) {
			//reload contacts from database for admin user
			$model = ORM::factory('Object_Contacts');
			$items = $model->where('object_id', ' = ', $object->id)->find_all();
			
			$twig->object->compiled['contacts'] = array();
			foreach($items as $item) {
				$twig->object->compiled['contacts'] []= array(
						'type' => $item->contact->contact_type_id,
						'value' => $item->contact->contact
					);
			}
		}

		//add to last views
		LastViews::instance()
			->set($object->id)
			->commit();

		$this->response->body($twig);
	}

	public function action_type89() {
		$object = $this->request->param("object");
		$url = $this->request->param("url");

		if ($url <> $this->request->get_full_url()) {
			HTTP::redirect($url, 301);
		}

		if ($object->active == 0) {
		   throw new HTTP_Exception_404;
		   return;
		}

		$twig = Twig::factory('detail/landing/index');
		$twig->domain      = $this->domain;
		$twig->city        = $this->domain->get_city();

		$detail_info = Detailpage::factory("Landing", $object)
							->get_crumbs()
							->get_landing_info()
							->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		
		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		
		$this->response->body($twig);
	}

	//новость
	public function action_type101() {
		$object = $this->request->param("object");
		$url = $this->request->param("url");

		if ($url <> $this->request->get_full_url()) {
			HTTP::redirect($url, 301);
		}

		if ($object->active == 0) {
		   throw new HTTP_Exception_404;
		   return;
		}

		$twig = Twig::factory('detail/news/index');
		$twig->domain      = $this->domain;
		$twig->city        = $this->domain->get_city();

		$detail_info = Detailpage::factory("Newsone", $object)
							->get_crumbs()
							->get_last_news(($twig->city) ? $twig->city->id : NULL)
							->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}

		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites
		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		
		$this->response->body($twig);
	}

	//купон
	public function action_type201() {
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

		$detail_info = Detailpage::factory("Kupon", $object)
							->get_crumbs()
							->get_kupon_info()
							->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		
		//favourites
		$twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
		//end favourites

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}
		

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
