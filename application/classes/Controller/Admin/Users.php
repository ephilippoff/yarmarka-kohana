<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Users extends Controller_Admin_Template {

	protected $module_name = 'user';

	public function action_index()
	{
		$limit  = 50;
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$users = ORM::factory('User');

		/**
		 * filters
		 */
		if ( ! $this->request->query('regdate')) // user regdate
		{
			// show users for today by default
			$users->where(DB::expr('date(regdate)'), '=', DB::expr("date '".date('Y-m-d')."'"));
		}
		else
		{
			$regdate = $this->request->query('regdate');
			if ($from_time = strtotime($regdate['from']))
			{
				$users->where(DB::expr('date(regdate)'), '>=', DB::expr("date '".date('Y-m-d', $from_time)."'"));
			}

			if ($to_time = strtotime($regdate['to']))
			{
				$users->where(DB::expr('date(regdate)'), '<=', DB::expr("date '".date('Y-m-d', $to_time)."'"));
			}
		}

		// invoices
		if ($this->request->query('has_invoices'))
		{
			$users->where('invoices_cnt', '>', 0);
		}

		// user role
		if ($role = intval($this->request->query('role')))
		{
			$users->where('role', '=', $role);
		}

		// user email
		if ($email = trim($this->request->query('email')))
		{
			$users->where('email', 'LIKE', '%'.$email.'%');
		}

		if ($phone = trim($this->request->query('phone')))
		{
			// @todo наверное это можно сделать на ORM как-то более изящно
			$users->where('', 'EXISTS', DB::expr('(SELECT object.id FROM object 
							LEFT JOIN object_contacts AS oc ON oc.object_id=object.id 
							JOIN contacts as c ON c.id = oc.contact_id 
							WHERE object.author="user"."id" AND c.contact_clear LIKE \'%'.$phone.'%\')'));
		}

		$clone_to_count = clone $users;
		$count_all = $clone_to_count->count_all();

		$users->offset($offset)
			->limit($limit);

		$sort_by	= trim($this->request->query('sort_by'));
		$direction	= trim($this->request->query('direction'));

		$sort_by = $sort_by ? $sort_by : 'regdate';
		$direction = $direction ? $direction : 'desc';

		$users->order_by($sort_by, $direction);

		$this->template->sort_by	= $sort_by;
		$this->template->direction	= $direction;
		$this->template->users		= $users->find_all();
		$this->template->roles		= ORM::factory('Role')
			->find_all()
			->as_array('id', 'name');
		$this->template->pagination	= Pagination::factory(array(
			'current_page'   => array('source' => 'query_string', 'key' => 'page'),
			'total_items'    => $count_all,
			'items_per_page' => $limit,
			'auto_hide'      => TRUE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'users',
			'action'     => 'index',
		));
	}

	public function action_add_settings()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		
		
		$this->template->errors = array();
		$cfg_categories = Kohana::$config->load('massload/bycategory');
		$categories = Array();

		foreach ($cfg_categories as $name=>$item)
			$categories[$name] = $item["name"];
		$this->template->categories = $categories;

		$type = $this->request->query('type');
		$filter_user_id = $this->request->query('user_id');
		$filter_user_email = $this->request->query('user_email');

		$user_settings = ORM::factory('User_Settings');


		if ($type)
			$user_settings = $user_settings->where("type","=",$type);
		if ($filter_user_id)
			$user_settings = $user_settings->where("user_id","=",$filter_user_id);
		if ($filter_user_email)
			$user_settings = $user_settings->join("user")
												->on("user.id","=","user_settings.user_id")
											->where("user.email","LIKE",'%'.$filter_user_email.'%');
		
		$user_settings_clone = clone $user_settings;
		$count_all = $user_settings_clone->count_all();
		
		$user_settings = $user_settings
								->order_by("user_id", "desc")
								->order_by("type", "asc")
								->order_by("name", "asc");		
		
		$user_settings = $user_settings
				->limit($limit)
				->offset($offset)
				->find_all();
		
		$this->template->user_settings =$user_settings;
		$this->template->user_settings_types = Kohana::$config->load('dictionaries.user_setting_types');
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'users',
				'action'     => 'add_settings',
			));		

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;
				$post['user_id'] = isset($post['user_id']) ? $post['user_id'] : 0;

				$post['category'] = isset($post['category']) ? $post['category'] : 0;

				$user = ORM::factory('User',(int) $post["user_id"]);
				if (!$user->loaded())
				{	
					$this->template->errors["user_id"] =  "Не верный user_id";
				} else {
					ORM::factory('User_Settings')->values($post)
					->save();	
					$this->redirect('khbackend/users/add_settings');
				}
				
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}

		}

	}

	public function action_delete_settings()
	{
		$this->auto_render = FALSE;

		$us = ORM::factory('User_Settings', $this->request->param('id'));

		if ( ! $us->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$us->delete();
				
		$this->redirect('khbackend/users/add_settings');

		//$this->response->body(json_encode(array('code' => 200)));
	
	}

	public function action_objectload()
	{
		$limit_oloads = $limit_priceload = 5;
		
		$page_oloads  = $this->request->query('page_oloads');
		$offset_oloads = ($page_oloads AND $page_oloads != 1) ? ($page_oloads-1) * $limit_oloads : 0;
		
		$page_priceload  = $this->request->query('page_priceload');
		$offset_priceload = ($page_priceload AND $page_priceload != 1) ? ($page_priceload-1) * $limit_priceload : 0;		
		
		$crontask 			= ORM::factory('Crontask');

		$this->template->states    = $crontask->get_states();
		$this->template->crontasks = $crontask->where("state","<>",5)
											->order_by("updated_on", "desc")
											->order_by("created_on", "desc")
											->limit(5)
											->find_all();

		$this->template->qstate = $qstate = $this->request->query('state');

		$objectload 		= ORM::factory('Objectload');
    	$objectload_files   = ORM::factory('Objectload_Files'); 

		if ($qstate)
			$oloads = $objectload->where("state","=",$qstate);
		
		$clone_oloads = clone $objectload;
		$total_oloads = $clone_oloads->count_all();
		
		$oloads	= $objectload
				->order_by("created_on", "desc")
				->limit($limit_oloads)
				->offset($offset_oloads)
				->find_all();
		
		
				
		$this->template->objectloads = $objectload->get_objectload_list($oloads);
		$this->template->states_ol   = $objectload->get_states();
		$this->template->categories  = Kohana::$config->load('massload/bycategory');
		$this->template->limit_oloads = $limit_oloads;
		$this->template->pagination_ol	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page_oloads'),
				'total_items'    => $total_oloads,
				'items_per_page' => $limit_oloads,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'users',
				'action'     => 'objectload',
			));			
		

		$priceloads = ORM::factory('Priceload');
		
		$clone_priceloads = clone $priceloads;
		$total_priceloads = $clone_priceloads->count_all();
		
		$priceloads = $priceloads->order_by("id","desc")
								->limit($limit_priceload)
								->offset($offset_priceload)
								->find_all();
		
		$this->template->priceloads = $priceloads;
		$this->template->limit_priceload = $limit_priceload;
		$this->template->pagination_pl	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page_priceload'),
				'total_items'    => $total_priceloads,
				'items_per_page' => $limit_priceload,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'users',
				'action'     => 'objectload',
			));			
		
		
	}

	public function action_objectload_shell()
	{
		$this->layout = 'shell';
	}

	public function action_crontask_to_archive()
	{
		$this->auto_render = FALSE;
		$post = $_POST;
		$ct = ORM::factory('Crontask', $post["id"]);

		if ( ! $ct->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$ct->state = 5;
		$ct->update();

		$json = array('code' => 200);
		$this->response->body(json_encode($json));
	}

	public function action_crontask_to_stop()
	{
		$this->auto_render = FALSE;
		$post = $_POST;
		$ct = ORM::factory('Crontask', $post["id"]);

		if ( ! $ct->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$ct->state = 4;
		$ct->update();

		$json = array('code' => 200);
		$this->response->body(json_encode($json));
	}

	public function action_objectload_refresh_statistic()
	{
		$this->auto_render = FALSE;
		$post = $_POST;
		$ol = ORM::factory('Objectload', $post["id"]);

		if ( ! $ol->loaded())
		{
			throw new HTTP_Exception_404;
		}
		$json = array('code' => 200);

		$ol->update_statistic();

		$ol = ORM::factory('Objectload', $post["id"]);
		
		$statistic = new Obj(unserialize($ol->statistic));
		$new = $statistic->loaded - $statistic->edited;
		$json["common"] = $new." / ".$statistic->edited." / ".$statistic->all." err:".$statistic->error;

		$of = ORM::factory('Objectload_Files')
					->where("objectload_id","=",$post["id"])
					->find_all();
		$files_stat = array();
		foreach ($of as $file) {
			$statistic = new Obj(unserialize($file->statistic));
			$new = $statistic->loaded - $statistic->edited;
			$flagend ='';
			if ($statistic->loaded + $statistic->error <> $statistic->all)
				$flagend = '<span style="color:red;">notended</span>';
			$files_stat[$file->id."_".$file->category] = $new." / ".$statistic->edited." / ".$statistic->all." err:".$statistic->error." ".$flagend;
		}

		$json["sub"] = $files_stat;

		if ($post["email"])
		{
			$objectload = new Objectload(NULL, $post["id"]);
			$objectload->sendReport($post["id"]);
		}
		
		$this->response->body(json_encode($json));
	}

	public function action_objectload_false_moderation()
	{
		$this->auto_render = FALSE;
		$post = $_POST;
		$ol = ORM::factory('Objectload', $post["id"]);

		if ( ! $ol->loaded())
		{
			throw new HTTP_Exception_404;
		}
		$json = array('code' => 200);

		$ol->comment = $post["text"];
		$ol->state = 3;
		$ol->update();
		$this->response->body(json_encode($json));
	}

	public function action_priceload_set_state()
	{
		$this->auto_render = FALSE;
		$post = $_POST;
		$pl = ORM::factory('Priceload', $post["id"]);

		if ( ! $pl->loaded())
		{
			throw new HTTP_Exception_404;
		}
		$json = array('code' => 200);

		$pl->comment = $post["text"];
		$pl->state = $post["state"];
		$pl->update();
		$this->response->body(json_encode($json));
	}

	

	public function action_ban()
	{
		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$json = array('code' => 200);

		$user->is_blocked	= 1;
		$user->block_reason	= trim($this->request->post('reason'));
		$user->save();

		$json['is_blocked'] = $user->is_blocked;

		$this->response->body(json_encode($json));
	}

	public function action_ban_and_unpublish()
	{
		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// block user
		$user->is_blocked	= 1;
		$user->block_reason	= trim($this->request->post('reason'));
		$user->save();

		// disable user ads
		$objects = $user->objects->find_all()->as_array(NULL, 'id');
		if ($objects)
		{
			ORM::factory('Object')
				->where('id', 'IN', $objects)
				->set('is_published', 0)
				->set('is_bad', 2)
				->update_all();
		}

		$json = array('code' => 200);
		$json['is_blocked'] = $user->is_blocked;

		$this->response->body(json_encode($json));
	}

	public function action_user_info()
	{
		$this->layout = 'admin_popup';

		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->user = $user;
	}

	public function action_change_role()
	{
		$this->layout = FALSE;
		$this->auto_render = FALSE;

		$user = ORM::factory('User', $this->request->post('user_id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$user->role = (int) $this->request->post('role');
		$user->save();
		
		return $this->response->body(json_encode(array("OK")));
	}

	public function action_ip_info()
	{
		$this->layout = 'admin_popup';

		$ip = trim($this->request->param('ip'));
		if ( ! Valid::ip($ip))
		{
			throw new HTTP_Exception_404;
		}

		$ipblock = ORM::factory('Ipblock')
			->where('ip', '=', $ip)
			->find();

		if (HTTP_Request::POST === $this->request->method())
		{
			$period = $this->request->post('period');
			if ( ! $ipblock->loaded())
			{
				$ipblock->ip	= $ip;
				$ipblock->text 	= $this->request->post('reason');
				$ipblock->expiration_date = $period 
					? date('Y-m-d H:i:s', strtotime("+{$period} days"))
					: NULL;
			}
			else
			{
				$ipblock->text 	= $this->request->post('reason');
				$ipblock->expiration_date = $period 
					? date('Y-m-d H:i:s', strtotime("+{$period} days", strtotime($ipblock->expiration_date)))
					: NULL;
			}
			$ipblock->save();
		}

		$this->template->ip			= $ip;
		$this->template->ipblock	= $ipblock;
		$this->template->users		= ORM::factory('User')
			->where('ip_addr', '=', $ip)
			->find_all();
		$this->template->objects	= ORM::factory('Object')
			->where('ip_addr', '=', $ip)
			->find_all();
	}

	public function action_delete()
	{
		$this->auto_render = FALSE;
		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$user->delete();

		$this->response->body(json_encode(array('code' => 200)));
	}

	public function action_login()
	{
		if (HTTP_Request::POST == $this->request->method())
		{
			$auth = Auth::instance();
			$remember = (bool) $this->request->post('remember');
			if ($auth->login($this->request->post('login'), $this->request->post('password'), $remember))
			{
				$this->redirect('khbackend');
			}
		}
	}

	public function action_moderation()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;			
		
		$filter = $this->request->query('filter');


		$s = trim(Arr::get($_GET, 's', ''));

		if ($s AND !$filter) $filter = 'all';

		$flags_moderation_query = DB::select("user_id")
								->from("user_settings")
								->where("type","=","orginfo")
								->where("name","=","moderate");
		
		$users = ORM::factory('User')
					->select(array("user_settings.value","moderate_state"), array("user_settings.created_on","moderate_on"))
					->join("user_settings")
						->on("user_settings.user_id","=","user.id")						
					->where("user.id","IN",$flags_moderation_query)
					->where("user.is_blocked","=",0)
					->where("user.org_type","=",2)
					->where("user_settings.type","=",'orginfo')
					->where("user_settings.name","=",'moderate');
		if (!$filter)
			$users = $users->where("user_settings.value","=",'0');
		elseif ($filter == "moderated")
			$users = $users->where("user_settings.value","=",'1');
		
		if ($s) 
		{	
			$users->where_open()
					->where(DB::expr('lower(org_name)'), 'like', '%'.mb_strtolower($s).'%')
					->or_where(DB::expr('lower(org_full_name)'), 'like', '%'.mb_strtolower($s).'%')
					->or_where(DB::expr('lower(org_inn)'), 'like', '%'.mb_strtolower($s).'%')
					->or_where(DB::expr('lower(email)'), 'like', '%'.mb_strtolower($s).'%')
					->where_close();			
		}		

		$this->template->s = $s;
		
		$users_clone = clone $users;
		$count_all = $users_clone->count_all();
		
		$users = $users->order_by("user_settings.created_on","desc");

		$this->template->estimates = Kohana::$config->load("dictionaries.estimate_for_org_info");
		$this->template->moderate_enable = (!$filter); 
		$this->template->users = $users
									->limit($limit)
									->offset($offset)
									->find_all();
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'users',
				'action'     => 'moderation',
			));		
	}

	public function action_orginfoinn_declineform()
	{
		$this->use_layout = FALSE;

		$this->decline_orginfoinn(2);
	}

	public function action_orginfoinn_decline()
	{
		$this->auto_render = FALSE;
		$json = array('code' => 400);

		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$reason = trim($this->request->post('reason'));

		if ($reason)
		{

			$user->org_inn 		 = NULL;
			$user->org_inn_skan  = NULL;
			$user->org_full_name = NULL;
			$user->save();

			$setting = ORM::factory('User_Settings')
							->where("user_id","=",$user->id)
							->where("name","=","moderate")
							->where("type","=","orginfo")
							->find();
			$setting->type = 'orginfo';
			$setting->value = 2;
			$setting->save();

			$user->org_moderate = 2;
			$user->save();


			$setting = ORM::factory('User_Settings')
							->where("user_id","=",$user->id)
							->where("name","=","moderate-reason")
							->where("type","=","orginfo")
							->find();

			$setting->user_id = $user->id;
			$setting->name = "moderate-reason";
			$setting->type = 'orginfo';
			$setting->value = $reason;
			$setting->save();

			if ($this->request->post('send_email') AND $user->loaded())
			{

				$params = array(
					'reason' => $reason,
				    'domain' => FALSE
				);

				Email_Send::factory('decline_orginfo')
					->to( $user->email )
					->set_params($params)
					->set_utm_campaign('decline_orginfo')
					->send();
			}

			if ($this->request->post('ban_user') AND $user->loaded())
			{
				$user->ban($reason);
			}
			
			$json['code'] = 200;
		}

		$this->response->body(json_encode($json));
	}

	private function decline_orginfoinn($state)
	{
		$this->template = View::factory('admin/users/decline_form');

		$user = ORM::factory('User', $this->request->param('id'));
		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->user 	= $user;
		$this->template->reasons = Kohana::$config->load("dictionaries.org_moderate_decline");
		$this->template->state  = $state;
	}
	
	public function action_registration_short()
	{
		$is_post = (HTTP_Request::POST === $this->request->method());
		$post_data = $this->request->post();
		$errors = array();
		$success = false;
		$user_id = 0;

		$type = 2;

		if ($is_post)
		{
			$post_data['login'] = strtolower(trim($post_data['login']));
			
			$validation = ORM::factory('User')->register_validation_short((array) $post_data);

			if ( !$validation->check())
			{
				$errors = $validation->errors('validation/auth');
			} 
			else 
			{
				try 
				{
					$user_id = ORM::factory('User')->registration_short( $post_data['login'], $post_data['pass'], $post_data['org_name'], $type );
				} 
				catch (Exception $e)
				{

				}
				
				$success = true;
			}
		}

		$this->template->params  = $post_data;
		$this->template->errors  = $errors;
		$this->template->success = $success;
		$this->template->user_id = $user_id;
	}	

	
} // End Admin_Users
