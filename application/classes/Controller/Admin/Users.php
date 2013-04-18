<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Users extends Controller_Admin_Template {

	protected $module_name = 'user';

	public function action_index()
	{
	}

	public function action_login()
	{
		if (HTTP_Request::POST == $this->request->method())
		{
			$auth = Auth::instance();
			if ($auth->login($this->request->post('login'), $this->request->post('password'), TRUE))
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
