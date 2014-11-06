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
		$this->template->errors = array();
		$cfg_categories = Kohana::$config->load('massload/bycategory');
		$categories = Array();

		foreach ($cfg_categories as $name=>$item)
			$categories[$name] = $item["name"];
		$this->template->categories = $categories;

		$user_settings = ORM::factory('User_Settings')
								->order_by("user_id", "desc")
								->order_by("name", "asc")
								->find_all();
		$this->template->user_settings =$user_settings;

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
		$crontask 			= ORM::factory('Crontask');

		$this->template->states    = $crontask->get_states();
		$this->template->crontasks = $crontask->where("state","<>",5)
											->order_by("updated_on", "desc")
											->order_by("created_on", "desc")
											->find_all();

		$this->template->qstate = $qstate = $this->request->query('state');

		$objectload 		= ORM::factory('Objectload');
    	$objectload_files   = ORM::factory('Objectload_Files'); 

		if ($qstate)
			$oloads = $objectload->where("state","=",$qstate);
		
		$oloads	= $objectload->order_by("created_on", "desc")
				->limit(50)
				->find_all();
				
		$this->template->objectloads = $objectload->get_objectload_list($oloads);
		$this->template->states_ol   = $objectload->get_states();
		$this->template->categories  = Kohana::$config->load('massload/bycategory');
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
} // End Admin_Users
