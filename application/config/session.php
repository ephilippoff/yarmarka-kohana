<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'session' => array(
		'lifetime' => 60*60*24*30,
	),
	'cookie' => array(
		'encrypted' => FALSE,
		'lifetime' => 60*60*24*30,
	),
);
