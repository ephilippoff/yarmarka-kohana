<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'mail_sending'		=> Kohana::$environment == Kohana::PRODUCTION,	// disabling mail sending for development may be usefull
	'main_domain'		=> 'yarmarka.dev',
	'default_region_id'	=> 73,
);
