<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Users extends Controller_Admin_Template {

	protected $module_name = 'user';

	public function action_index()
	{
		$limit  = 20;
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$users = ORM::factory('User')
			->offset($offset)
			->order_by('regdate', 'desc')
			->limit($limit);

		$this->template->users = $users->find_all();
		$this->template->pagination = Pagination::factory(array(
			'current_page'   => array('source' => 'query_string', 'key' => 'page'),
			'total_items'    => $users->count_all(),
			'items_per_page' => $limit,
			'auto_hide'      => FALSE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'users',
			'action'     => 'index',
		));
	}

	public function action_login()
	{
		if (HTTP_Request::POST == $this->request->method())
		{
			$auth = Auth::instance();
			if ($auth->login($this->request->post('login'), $this->request->post('password'), $this->request->post('remember')))
			{
				$this->redirect('khbackend');
			}
		}
	}

	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('khbackend');
	}
} // End Admin_Users
