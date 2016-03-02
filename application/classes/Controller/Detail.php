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
		$twig->onPageFlag = 'detail';
		$twig->horizontalView = TRUE;

		$detail_info = Detailpage::factory("Default", $object)
						->get_messages()
						->get_similar()
						->get_crumbs()
						->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}

		$twig->isJobVacancy = $detail_info->category->seo_name == 'vakansii';
		if ($twig->isJobVacancy && isset($_GET['cv'])) {
			$cv_object = ORM::factory('Object', $_GET['cv']);
			if ($cv_object->loaded()) {
				$cv_object_category = ORM::factory('Category', $cv_object->category);
				if ($cv_object_category->loaded() && $cv_object_category->seo_name == 'spetsialisty') {
					$twig->cvAttachedUrl = $cv_object->get_full_url();
				}
			}
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
		$cUser = \Yarmarka\Models\User::current();
		$twig->isGuest = $cUser->getIsGuest();
		$twig->currentUri = $this->request->uri();
		$twig->userEmail = $cUser->getEmail();

		//add to last views
		LastViews::instance()->set($object->id);
		$this->response->body($twig);
		LastViews::instance()->commit();
	}

	protected function validate_cv_mode($categorySeoName) {
		$object = ORM::factory('Object', $_GET['object_id']);
		if (!$object->loaded()) {
			throw new HTTP_Exception_404;
		}
		$category = ORM::factory('Category', $object->category);
		if (!$category->loaded() || $category->seo_name != $categorySeoName) {
			throw new HTTP_Exception_404;
		}

		return $object;
	}

	public function enable_cv_mode() {
		$object = $this->validate_cv_mode('vakansii');
		$session = Session::instance();
		$session->set('cv_mode', 1);
		$session->set('cv_mode_parent', $object->id);
		return $object;
	}

	public function disable_cv_mode() {
		$object = $this->validate_cv_mode('spetsialisty');
		$session = Session::instance();
		$session->set('cv_mode', 0);
		$parent_object = ORM::factory('Object', $session->get('cv_mode_parent'));
		if (!$parent_object->loaded()) {
			throw new Exception('parent_object not found!');
		}
		$session->set('cv_mode_parent', NULL);
		return $parent_object;
	}

	public function action_select_cv() {
		$this->enable_cv_mode();
		$this->redirect('/user/published/rabota/spetsialisty?cv_mode=1');
		die;
	}

	public function action_create_cv() {
		$this->enable_cv_mode();
		$this->redirect('/add?cv_mode=1');
		die;
	}

	public function action_use_cv() {
		$object = $this->disable_cv_mode();
		$this->redirect($object->get_full_url() . '?cv=' . (int) $_GET['object_id']);
		die;
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


		$twig->horizontalView = TRUE;
		$twig->staticMainMenu = TRUE;
		$twig->reverse = TRUE;
		$twig->onPageFlag = 'detail';

		$detail_info = Detailpage::factory("Newsone", $object)
							->get_crumbs()
							->get_last_news(($twig->city) ? $twig->city->id : NULL)
							->get();

		foreach ((array) $detail_info as $key => $item) {
			$twig->{$key} = $item;
		}

		$premium_kupons = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "expiration" => TRUE,
                "premium" => TRUE,
                "category_id" => array(173),
                "city_id" => ($twig->city->id) ? array($twig->city->id) : NULL,
            ),
            array("limit" => 3, "order" => "date_expired")
        );

        $twig->premium_kupons = Search::getresult($premium_kupons->execute()->as_array());

        $kupons = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "expiration" => TRUE,
                "category_id" => array(173),
                "city_id" => ($twig->city->id) ? array($twig->city->id) : NULL,
            ),
            array("limit" => 3, "order" => "date_expired")
        );

        $twig->kupons = Search::getresult($kupons->execute()->as_array());

        $attachments = ORM::factory('Object_Attachment')
                            ->order_by("id","desc")
                            ->limit(3)
                            ->getprepared_all();
        $promo_thumbnails = array_map(function($item){
            return Imageci::getSavePaths($item->filename);
        }, $attachments);
        $twig->promo_thumbnails = $promo_thumbnails;

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

		$twig->testKuponLink = NULL;
		//get kupon group
		$kuponGroup = ORM::factory('Kupon_Group')
			->where('object_id', '=', $object->id)
			->find();
		if ($kuponGroup->loaded()) {
			//get first kupon
			$kupon = ORM::factory('Kupon')
				->where('kupon_group_id', '=', $kuponGroup->id)
				->find();
			if ($kupon->loaded()) {
				$twig->testKuponLink = \Yarmarka\Models\User::current()->isAdminOrModerator()
					? '/kupon/print/' . $kupon->id
					: NULL;
			}
		}

		LastViews::instance()->set($object->id);
		$this->response->body($twig);
		LastViews::instance()->commit();
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
