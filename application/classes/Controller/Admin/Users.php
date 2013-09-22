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
