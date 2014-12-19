<?php defined('SYSPATH') OR die('No direct script access.');

class Admin
{

	static function send_error($title, $message)
	{
		if (is_array($message))
			$message = implode("</br>", $message);
		
		Kohana::$log->add(Log::ERROR, $message);
		Email::send( Kohana::$config->load('common.admin_emails'), 
					 Kohana::$config->load('email.default_from'), 
					 $title, 
					 $message);

	}

}