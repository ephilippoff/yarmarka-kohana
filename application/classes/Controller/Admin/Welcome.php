<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Welcome extends Controller_Admin_Template {

	public function action_index()
	{
	}
	
	public function action_logout()
	{
		Auth::instance()->logout();
		$this->redirect('khbackend');
	}

} // End Admin_Welcome
