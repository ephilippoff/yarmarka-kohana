<?php defined('SYSPATH') or die('No direct script access.');

class Task_Mailsender extends Minion_Task
{
	protected function _execute(array $params)
	{		
		//$users = ORM::factory('User')->where('is_blocked', '=', 0)->find_all()->as_array('id', 'email');
		//check mail
		$users = ORM::factory('User')->where('email', '=', 'd.Istomin@yarmarka.biz')->find_all()->as_array('id', 'email');
		
		foreach ($users as $email) 
		{
			$msg = View::factory('emails/newyear')->render();			
			Email::send(trim($user->email), Kohana::$config->load('email.default_from'), "Ярмарка поздравляет Вас с Новым Годом!", $msg);
		}				
	}

}