<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Phones extends Controller_Admin_Template {

	public function action_index()
	{
		$this->redirect('khbackend/phones/list');
	}

	public function action_get_users() {

		/* enable ajax mode */
		$this->auto_render = false;

		/* set pagination schema */
		$pagination = array(
				'page' => 1,
				'perPage' => 20
			);

		/* get pagination parameters from query */
		foreach($pagination as $key => $value) {
			if (isset($_REQUEST[$key])) {
				$pagination[$key] = (int) $_REQUEST[$key];
			}
		}

		/* calc offset and limit from pagination */
		$offset = ($pagination['page'] - 1) * $pagination['perPage'];
		$limit = $pagination['perPage'];

		/* query database */
		$query = ORM::factory('User');
		if (!empty($_REQUEST['s'])) {
			$query = $query
				->where_open()
				->where('email', 'like', '%' . $_REQUEST['s'] . '%')
				->or_where('', '',
					DB::expr(
						'exists('
							. 'select *'
							. ' from user_contacts a'
							. ' inner join contacts c on c.id = a.contact_id'
							. ' where'
								. ' a.user_id = "user".id'
								. ' and ('
									. ' c.contact like \'%' . $_REQUEST['s'] . '%\''
									. ' or c.contact_clear like \'%' . $_REQUEST['s'] . '%\''
								. ')'
						. ')'
					))
				->where_close();
		}
		$queryCopy = clone $query;
		/* calculate count */
		$pagination['totalItems'] = $query->count_all();
		$pagination['totalPages'] = ceil($pagination['totalItems'] / $pagination['perPage']);

		$queryRes = $queryCopy
			->offset($offset)
			->limit($limit)
			->find_all();
		$items = array();
		foreach($queryRes as $item) {
			$items []= $item->as_array();
		}

		/* prepare answer */
		$answer = array(
				'pagination' => $pagination,
				'items' => $items,
				'offset' => $offset,
				'limit' => $limit,
				//'query' => Database::instance()->last_query
			);

		$this->response->body(json_encode($answer));
	}

	public function action_bind() {

		/* enable ajax mode */
		$this->auto_render = false;

		/* validate parameters */
		$required = array( 'user_id', 'contact_id' );
		foreach($required as $key) {
			if (empty($_REQUEST[$key])) {
				throw new Exception($key);
			}
			$_REQUEST[$key] = (int) $_REQUEST[$key];
		}

		/* search user */
		$user = ORM::factory('User')
			->where('id', '=', $_REQUEST['user_id'])
			->find();

		if (!$user->loaded()) {
			throw new Exception('Bad user_id');
		}

		/* search contact */
		$contact = ORM::factory('Contact')
			->where('id', '=', $_REQUEST['contact_id'])
			->find();

		if (!$contact->loaded()) {
			throw new Exception('Bad contact_id');
		}

		/* drop link if exists */
		DB::delete('user_contacts')
			->where('contact_id', '=', $contact->id)
			->execute();

		/* append link */
		DB::insert('user_contacts', array( 'contact_id', 'user_id' ))
			->values(array( $_REQUEST['contact_id'], $_REQUEST['user_id'] ))
			->execute();

		/* set contact verified user id */
		$contact->verified_user_id = $user->id;
		$contact->save();

		/* output */
		$this->response->body(json_encode(array( 'res' => true )));
	}

	public function action_list()
	{
		$limit  = 50;
		$page   = $this->request->query('page');
		$status = $this->request->query('status');
		$phone  = $this->request->query('phone');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$contacts = ORM::factory('Contact')
			->where('contact_type_id', 'IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE));

		if ($phone)
		{
			$contacts->where_open()
				->where('contact_clear', 'LIKE', '%'.$phone.'%')
				->or_where('contact', 'LIKE', '%'.$phone.'%')
				->where_close();
		}

		switch ($status)
		{
			case 'moderate':
				$contacts->where('moderate', '=', 0)
					->where('verified_user_id', 'IS', DB::expr('NOT NULL'));
			break;

			case 'blocked':
				$contacts->where('blocked', '=', 1);
			break;

			case 'verified':
				$contacts->where('verified_user_id', 'IS', DB::expr('NOT NULL'));
			break;
		}

		$clone_to_count = clone $contacts;
		$count_all = $clone_to_count->count_all();

		$contacts->offset($offset)
			->limit($limit)
			->order_by('id', 'desc');

		$this->template->statuses 	= array(
			'moderate' 	=> 'На модерации',
			'blocked'	=> 'Заблокированные',
			'verified'	=> 'Верифицированные',
		);
		$this->template->contacts	= $contacts->find_all();
		$this->template->pagination	= Pagination::factory(array(
			'current_page'   => array('source' => 'query_string', 'key' => 'page'),
			'total_items'    => $count_all,
			'items_per_page' => $limit,
			'auto_hide'      => TRUE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'phones',
			'action'     => 'list',
		));
	}

	public function action_buttons()
	{
		$this->use_layout = FALSE;
		$this->template->contact = ORM::factory('Contact', $this->request->param('id'));
	}

	public function action_moderation()
	{
		$limit  = 50;
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$contacts = ORM::factory('Contact')
			->where('contact_type_id', 'IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE))
			->where('verified_user_id', 'IS', DB::expr('NOT NULL'))
			->where('moderate', '=', 0);

		$clone_to_count = clone $contacts;
		$count_all = $clone_to_count->count_all();

		$contacts->offset($offset)
			->limit($limit)
			->order_by('id', 'desc');

		$this->template->contacts	= $contacts->find_all();
		$this->template->pagination	= Pagination::factory(array(
			'current_page'   => array('source' => 'query_string', 'key' => 'page'),
			'total_items'    => $count_all,
			'items_per_page' => $limit,
			'auto_hide'      => TRUE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'phones',
			'action'     => 'moderation',
		));
	}

	public function action_confirm()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$contact->moderate = 1;
		$contact->save();

		$this->response->body(json_encode($json));
	}

	public function action_decline()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$objects = ORM::factory('Object')
			->join('object_contacts')
			->on('object.id', '=', 'object_contacts.object_id')
			->where('contact_id', '=', $contact->id)
			->where('author', '=', $contact->verified_user_id)
			->find_all();


		$city_id = FALSE;

		foreach ($objects as $object)
		{
			$city_id = $object->city_id;

			$object->is_published = 0;
			$object->save();

			DB::delete('object_contacts')
				->where('object_id', '=', $object->id)
				->where('contact_id', '=', $contact->id)
				->execute();
		}

		if ($contact->verified_user->email)
		{
			$params = array(
			    'phone' => $contact->contact,
			    'objects' => $objects,
			    'domain' => $city_id
			);

			Email_Send::factory('decline_contact')
		    			->to( $contact->verified_user->email )
		    			->set_params($params)
		    			->set_utm_campaign('decline_contact')
		    			->send();
		}

		$contact->verified_user->delete_contact($contact->id);
		$contact->verified_user_id = DB::expr('NULL');
		$contact->save();

		$this->response->body(json_encode($json));
	}

	public function action_block()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$objects = ORM::factory('Object')
			->join('object_contacts')
			->on('object.id', '=', 'object_contacts.object_id')
			->where('contact_id', '=', $contact->id)
			->where('author', '=', $contact->verified_user_id)
			->find_all();

		$city_id = FALSE;

		foreach ($objects as $object)
		{
			$city_id = $object->city_id;

			$object->is_published = 0;
			$object->save();

			DB::delete('object_contacts')
				->where('object_id', '=', $object->id)
				->where('contact_id', '=', $contact->id)
				->execute();
		}

		if ($contact->verified_user->email)
		{

			$params = array(
			    'phone' => $contact->contact,
			    'objects' => $objects,
			    'domain' => $city_id
			);

			Email_Send::factory('block_contact')
				    			->to( $contact->verified_user->email )
				    			->set_params($params)
				    			->set_utm_campaign('block_contact')
				    			->send();
				    	
		}

		$contact->verified_user->delete_contact($contact->id);
		$contact->verified_user_id = DB::expr('NULL');
		$contact->blocked = 1;
		$contact->save();

		$this->response->body(json_encode($json));
	}

	public function action_unblock()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$contact->blocked = 0;
		$contact->save();

		$this->response->body(json_encode($json));
	}

	public function action_followme()
	{
		$this->template->errors = array();
		if (HTTP_Request::POST === $this->request->method()) 
		{
						
				$post = $_POST;
				$post['contact'] = isset($post['contact']) ? $post['contact'] : 0;
				$contact = ORM::factory('Contact')
								->where("contact_clear","=",$post["contact"])
								->find();
				if (!$contact->loaded())
				{	
					$this->template->errors["contact"] =  "Телефон отсутствует в базе";
				} else {
					$cf = ORM::factory('Contact_Followme')
							->where("contact_id","=",$contact->id)->find();
					$cf->contact_id = $contact->id;
					$cf->sid_id = $post["sid_id"];
					$cf->save();	
					$this->redirect('/khbackend/phones/followme');
				}

		}

		$this->template->sids = Kohana::$config->load("followme.sids");
		$this->template->list = ORM::factory('Contact_Followme')->find_all();
	}

	public function action_setfollowme()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		$json = array('code' => 200);
		
		$id = $this->request->post("id");

		$config = Kohana::$config->load("followme");
		$config = new Obj($config);
		
		$sids = DB::select("sid_id")
						->from("contact_followme")
						->where("active","=","t")
						->group_by("sid_id")
						->execute()->as_array("sid_id");

		$sids = array_keys($sids);
		
		$result = array();
		foreach ($sids as $sid_id) {
			$numbers = array();
			$cf = ORM::factory('Contact_Followme')
					->where("sid_id","=", $sid_id."")
					->where("active","=","t")
					->find_all();

			foreach ($cf as $number){
				$numbers[] = array(
					"timeout" => 60,
					"redirect_number" => "+".$number->contact->contact_clear,
					"name" => "+".$number->contact->contact_clear,
					"active" => "Y",
					"period" => "always",
					"period_description" => "Always",
					"follow_order" => 1
				);
			}

			$params = array(
				$sid_id,
				$numbers
			);

			$client = new Jsonrpc($config->url);
			$client->debug = true;
			$client->authentication($config->login, $config->pass);
			$result[] = new Obj($client->execute('setFollowme', $params));
		}
		
		echo Debug::vars($result);

		$this->response->body(json_encode($json));
	}

	public function action_getfollowme()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		$json = array('code' => 200);
		
		$id = $this->request->post("id");
		$config = Kohana::$config->load("followme");
		$config = new Obj($config);
		$contact = ORM::factory('Contact_Followme',$id);

		$params = array($contact->sid_id);

		$client = new Jsonrpc($config->url);
		$client->debug = true;
		$client->authentication($config->login, $config->pass);
		$result = new Obj($client->execute('getFollowme', $params));

		echo Debug::vars($result);

		$this->response->body(json_encode($json));

	}

	public function action_followme_statistic()
	{

	}
}

/* End of file Phones.php */
/* Location: ./application/classes/Controller/Admin/Phones.php */