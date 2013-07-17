<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Welcome extends Controller_Admin_Template {

	public function action_index()
	{
		var_dump(Kohana::$config->load('common.main_domain'));
	}

} // End Admin_Welcome
