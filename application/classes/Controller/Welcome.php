<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Template {

	public function action_index()
	{
		$user = ORM::factory('User', 318069);
		if ($user->loaded())
		{
			var_dump($user->fullname);
		}
	}

} // End Welcome
