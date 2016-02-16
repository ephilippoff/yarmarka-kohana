<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Objects extends Controller_Admin_Template {

	protected $module_name = 'object';

	 public function before()
	{
		parent::before();

		$this->domain = new Domain();

	}

	public function action_index()
	{

		if ($user = Auth::instance()->get_user() AND $user->role == 1) {
			Kohana::$profiling = TRUE;
		} else {
			Kohana::$profiling = FALSE;
		}

		$filters_enable = TRUE;

		$search_filters = array(
			"active" => TRUE,
			"compile_exists" => TRUE
		);

		if ($user_id = intval($this->request->query('user_id')))
		{
			$filters_enable = FALSE;

			$this->template->author = ORM::factory('User', $user_id);
			$search_filters["user_id"] = $user_id;
		} 
		elseif ($filters_enable AND $email = trim(mb_strtolower($this->request->query('email'))))
		{
			$filters_enable = FALSE;

			if (is_numeric($email)) // can be email or object id
			{
				$search_filters["id"] = $email;
			}
			else
			{
				$search_filters["email"] = $email;
			}
		} 
		elseif ($filters_enable AND $contact = trim(mb_strtolower($this->request->query('contact'))))
		{
			$filters_enable = FALSE;

			$search_filters["contact"] = array(
				"clear" => ( Valid::email( $contact ) ) ? $contact : Text::clear_phone_number($contact),
				"raw" => $contact
			);
		} elseif ($filters_enable AND $without_attribute_id = intval($this->request->query('without_attribute_id'))) {

			$filters_enable = FALSE;
			
			$search_filters["without_attribute"] = $without_attribute_id;

		} else {
			$query = $this->request->query();

			unset($query["page"]);
			unset($query["limit"]);
			if (count($query) == 0) 
			{
				$search_filters["source"] = 1;
				$search_filters["moder_state"] = 0;
				$search_filters["user_role"] = 2;

				$search_filters["real_date_created"] = array();
				$search_filters["real_date_created"]["from"] = date('Y-m-d', strtotime('-7 days'));
			}
		}

		if ($filters_enable AND $date = $this->request->query('date'))
		{
			$field = $this->request->query('date_field');
			if ($field == 'date_created') {
				unset($search_filters["real_date_created"]);
				$search_filters["date_created"] = array();
				if ($from_time = strtotime($date['from']))
				{
					$search_filters["date_created"]["from"] = date('Y-m-d', $from_time);
				}

				if ($to_time = strtotime($date['to']))
				{
					$search_filters["date_created"]["to"] = date('Y-m-d', $to_time);
				} 
			}
			else {
				unset($search_filters["date_created"]);
				$search_filters["real_date_created"] = array();
				if ($from_time = strtotime($date['from']))
				{
					$search_filters["real_date_created"]["from"] = date('Y-m-d', $from_time);
				}

				if ($to_time = strtotime($date['to']))
				{
					$search_filters["real_date_created"]["to"] = date('Y-m-d', $to_time);
				} 
			}
		}

		if ($filters_enable AND $category_id = intval($this->request->query('category_id')))
		{
			$search_filters["category_id"] = $category_id;
		}

		if ($filters_enable AND $city_id = intval($this->request->query('city_id')))
		{
			$search_filters["city_id"] = $city_id;
		}

		if ($filters_enable AND $user_role = $this->request->query('user_role'))
		{
			$search_filters["user_role"] = $user_role;
		}

		if ($filters_enable AND $this->request->query('moder_state') != "")
		{
			$moder_state = intval($this->request->query('moder_state'));

			if ($moder_state == 3)
			{
				$search_filters["complaint_exists"] = TRUE;
			} 
			else 
			{
				$search_filters["moder_state"] = $moder_state;
			}
			
		}

		

		if ($filters_enable AND $source = intval($this->request->query('source')) )
		{
			$search_filters["source"] = $source;
		}

		//additional filters

		//generate schema
		$additionalFilters = array(
				'obj_type' => array(
						'label' => 'Тип объекта',
						'value' => NULL,
						'pushProperty' => 'type_tr',
						'items' => array(
								array( 'label' => 'Все', 'value' => '' ),
								array( 'label' => 'Рекламное объявление', 'value' => 89 ),
								array( 'label' => 'Новость', 'value' => 101 ),
								array( 'label' => 'Статья', 'value' => 102 ),
								array( 'label' => 'Купон', 'value' => 201 ),
							)
					),
				'text' => array( 'label' => 'Текст', 'value' => '', 'pushProperty' => 'user_text' ),
				'expired' => array( 'label' => 'Отложенные', 'value' => NULL, 'pushProperty' => 'expirationInverse' ),
			);

		//process user values
		if (array_key_exists('additional', $_REQUEST)) {
			foreach($_REQUEST['additional'] as $key => $value) {
				if (!array_key_exists($key, $additionalFilters)) {
					continue;
				}

				$additionalFilters[$key]['value'] = $value;
				if (!empty($value)) {
					$search_filters[$additionalFilters[$key]['pushProperty']] = $value;
				}
			}
		}

		//save filters to template
		$this->template->additionalFilters = $additionalFilters;
		// additional filters done

		$search_params = array(
			"page" => $this->request->query('page') ? $this->request->query('page') : 1,
			"limit" => $this->request->query('limit') ? $this->request->query('limit') : 30,
			"order" => trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : $this->request->query('date_field'),
			"order_direction" => trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc'
		);

		$sort_by =  $search_params["order"];
		$direction =  $search_params["order_direction"];
		$limit =  $search_params["limit"];

		$main_search_query = Search::searchquery($search_filters, $search_params);
		$main_search_result = Search::getresult($main_search_query->execute()->as_array());
		$ids = array_map(function($item){
			return $item["id"];
		}, $main_search_result);
		if (count($ids) == 0) $ids = array(0);

		$author_ids = array_map(function($item){
			return $item["author"];
		}, $main_search_result);
		if (count($author_ids) == 0) $author_ids = array(0);
		
		$main_search_result_count = Search::searchquery($search_filters, array(), array("count" => TRUE))
													->execute()
													->get("count");
		
		
		$this->template->objects = ORM::factory('Object')->where("id","IN", $ids)->find_all();
		$this->template->object_compiled	= array();

		foreach ($main_search_result as $key => $value) {
			$this->template->object_compiled[$value["id"]] = $value;
		}

		//pagination
		$this->template->pagination	= Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $main_search_result_count,
			'items_per_page' => $search_params['limit'],
			'auto_hide' => TRUE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'objects',
			'action'     => 'index'
		));

		$this->template->sort_by 	= $sort_by;
		$this->template->direction 	= $direction;
		$this->template->limit = $limit;

		$object_contacts = ORM::factory('Object_Contact')
			->select(array("contact","contact_value"), 'contact_clear','contact_type_id' )
			->join("contacts")
				->on("object_contact.contact_id","=","contacts.id")
			->where("object_id", "IN", $ids )
			->find_all();

		$_contacts = array();
		foreach ($object_contacts as $contact) {
			if ( !isset($_contacts[$contact->object_id]) ) {
				$_contacts[$contact->object_id] = array();
			}

			$_contacts[$contact->object_id][] = $contact->get_row_as_obj();
		}
		$this->template->object_contacts = $_contacts;

		$this->template->complaints = ORM::factory('Complaint')
			->where("object_id", "IN", $ids )
			->find_all()
			->as_array("id");

		$this->template->users = ORM::factory('User')
			->where("id", "IN", $author_ids )
			->find_all()
			->as_array('id', 'email','role');

		$this->template->roles = ORM::factory('Role')
			->find_all()
			->as_array('id', 'name');

		$this->template->categories = ORM::factory('Category')
			->order_by('title')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'title');

		$this->template->cities = ORM::factory('City')
			->where('is_visible',"=",1)
			->order_by('title')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'title');

		$this->template->attributes = ORM::factory('Attribute')
			->where('type',"=","list")
			->order_by('seo_name')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'seo_name');



		$this->template->search_filters = $search_filters;
	}

	public function action_ajax_change_moder_state()
	{
		$this->auto_render = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (intval($this->request->post('moder_state')))
		{
			$object->moder_state 	= 1;
			// $object->is_published 	= 1;
			$object->is_bad 		= 0;
		}
		else
		{
			$object->moder_state 	= 0;
		}
		$object->save();
		
		// moderation log
		$m_log = ORM::factory('Object_Moderation_Log');
		$m_log->action_by 	= Auth::instance()->get_user()->id;
		$m_log->user_id 	= $object->author;
		$m_log->description = $object->moder_state ? "Прошло модерацию" : "На модерации" ;
		$m_log->reason 		= "STATUS".$object->moder_state;
		$m_log->object_id 	= $object->id;
		$m_log->save();		
	}

	public function action_ajax_decline()
	{
		$this->use_layout = FALSE;

		$this->decline_form(1);
	}

	public function action_not_show_on_index()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		$value = $this->request->post('value');

		$json = array();

		$json['code'] = 200;
		$json['result'] = ($value == 'true')? TRUE : FALSE;

		$object->not_show_on_index = $json['result'];
		$object->save();

		$this->response->body(json_encode($json));
	}

	public function action_ajax_ban()
	{
		$this->use_layout = FALSE;

		$this->decline_form(2);
	}

	public function action_ajax_delete($value='')
	{
		$this->use_layout = FALSE;

		$this->decline_form(0);
	}

	private function decline_form($is_bad)
	{
		$this->template = View::factory('admin/objects/decline_form');

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object 	= $object;
		$this->template->reasons 	= ORM::factory('Object_Reason')->find_all()->as_array('id', 'full_text');
		$this->template->is_bad 	= $is_bad;
	}

	public function action_decline()
	{
		$this->auto_render = FALSE;
		$json = array('code' => 400);

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$reason = trim($this->request->post('reason'));
		$is_bad = intval($this->request->post('is_bad'));

		if ($is_bad == 1)
		{
			$description = "Заблокировано до исправления по причине : $reason";
		} 
		elseif ($is_bad == 2)
		{
			$description = "Заблокировано окончательно по причине : $reason";
		}
		else
		{
			$description = "Удалено по причине: $reason";
		}

		if ($reason)
		{
			// moderation log
			$m_log = ORM::factory('Object_Moderation_Log');
			$m_log->action_by 	= Auth::instance()->get_user()->id;
			$m_log->user_id 	= $object->author;
			$m_log->description = $description;
			$m_log->reason 		= $reason;
			$m_log->object_id 	= $object->id;
			$m_log->save();

			// msg to user
			ORM::factory('User_Messages')->add_msg_to_object($object->id, $description);

			if ($this->request->post('send_email') AND $object->user->loaded())
			{
				$msg = View::factory('emails/manage_object', 
					array(
						'UserName' => $object->user->fullname ? $object->user->fullname : $object->user->login,
						'actions' => array(
							$description . ' ('.HTML::anchor($object->get_url(), $object->title).')',
						),
					)
				)->render();
				Email::send(trim($object->user->email), Kohana::$config->load('email.default_from'), "Сообщение от модератора сайта", $msg);
			}
						

			if ($is_bad)
			{
				$object->is_published 	= 0;
				$object->is_bad 		= $is_bad;
				$object->moder_state 	= 1;
				$object->save();
			}
			else
			{
				$object->active 		= 0;
				$object->is_published 	= 0;
				$object->moder_state 	= 1;
				$object->save();
			}
			$json['code'] = 200;
		}

		$this->response->body(json_encode($json));
	}

	public function action_row()
	{
		$this->use_layout = FALSE;


		$main_search_query = Search::searchquery(array("id" => $this->request->param('id')), array());
		$main_search_result = Search::getresult($main_search_query->execute()->as_array());
		$ids = array_map(function($item){
			return $item["id"];
		}, $main_search_result);

		$author_ids = array_map(function($item){
			return $item["author"];
		}, $main_search_result);

		if (count($author_ids) == 0) $author_ids = array(0);

		$this->template->object = ORM::factory('Object')->where("id","IN", $ids)->find();
		$this->template->compiled = $main_search_result[0]['compiled'];

		$object_contacts = ORM::factory('Object_Contact')
			->select(array("contact","contact_value"), 'contact_clear','contact_type_id' )
			->join("contacts")
				->on("object_contact.contact_id","=","contacts.id")
			->where("object_id", "IN", $ids )
			->find_all();

		$_contacts = array();
		foreach ($object_contacts as $contact) {
			if ( !isset($_contacts[$contact->object_id]) ) {
				$_contacts[$contact->object_id] = array();
			}

			$_contacts[$contact->object_id][] = $contact->get_row_as_obj();
		}
		$this->template->object_contacts = $_contacts;//[$this->template->object->id];

		$this->template->complaints = ORM::factory('Complaint')
			->where("object_id", "IN", $ids )
			->find_all()
			->as_array("id");

		$this->template->users = ORM::factory('User')
			->where("id", "IN", $author_ids )
			->find_all()
			->as_array('id', 'email','role');

		$this->template->roles = ORM::factory('Role')
			->find_all()
			->as_array('id', 'name');

		$this->template->categories = ORM::factory('Category')
			->order_by('title')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'title');

		$this->template->cities = ORM::factory('City')
			->where('is_visible',"=",1)
			->order_by('title')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'title');

		// $params = array(
		// 	'object' => $object, 
		// 	'compiled' => $compiled, 
		// 	'categories' => $categories, 
		// 	'cities' => $cities,
		// 	'users' => $users,
		// 	'complaints' => $complaints,
		// 	'object_contacts' => $object_contacts,
		// 	'roles' => $roles
		// );
		$user_role = Auth::instance()->get_user()->role;
		$this->template->user_role_admin = (in_array($user_role, array(1,5,9))) ? TRUE: FALSE;
	}

	public function action_edit()
	{
		$this->use_layout = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object = $object;
	}

	public function action_save()
	{
		$this->auto_render = FALSE;
		$json = array('code' => 400);

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$title = $this->request->post('title');
		$user_text = $this->request->post('user_text');

		if ( ! $title OR ! $user_text)
		{
			$json['errors'] = 'Заполните все поля';
		}
		else
		{
			$object->title 		= trim($title);
			$object->user_text 	= trim($user_text);
			$object->save();

			$json['code'] = 200;
		}

		$this->response->body(json_encode($json));
	}

	public function action_complaints()
	{
		$this->use_layout = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object = $object;
	}
	
	public function action_ajax_archive()
	{
		$this->auto_render = FALSE;
		
		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		$object->in_archive	= 'f';
		$object->save();
		
		// moderation log
		$m_log = ORM::factory('Object_Moderation_Log');
		$m_log->action_by 	= Auth::instance()->get_user()->id;
		$m_log->user_id 	= $object->author;
		$m_log->description = "Снята архивация";
		$m_log->reason 		= '';		
		$m_log->object_id 	= $object->id;
		$m_log->save();		
		
		$json['code'] = 200;
		
		$this->response->body(json_encode($json));		
	}	
	
	public function action_moderation_log()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		$object_id = Arr::get($_GET, 'object_id', '');

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
			
		$list = ORM::factory('Object_Moderation_Log')
				->with_moderator();

		//Поиск
		if ($object_id) 
		{	
			$list->where('object_id', '=', (int)$object_id);			
		}		
		// количество общее
		$clone_to_count = clone $list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$list->order_by($sort_by, $sort);		

		$list->limit($limit)->offset($offset);
		
		// order
		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'id';

		$this->template->list = $list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->object_id = $object_id;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'objects',
				'action'     => 'moderation_log',
			));		
	}
	
}

/* End of file Objects.php */
/* Location: ./application/classes/Controller/Admin/Objects.php */