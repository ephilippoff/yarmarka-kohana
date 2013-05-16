<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'merge'      => array(),
	'folder'     => 'assets',
	'load_paths' => array(
		Assets::JAVASCRIPT => DOCROOT.'js'.DIRECTORY_SEPARATOR.'adaptive'.DIRECTORY_SEPARATOR,
		Assets::STYLESHEET => DOCROOT.'css'.DIRECTORY_SEPARATOR.'adaptive'.DIRECTORY_SEPARATOR,
	),
	'processor'  => array(
		Assets::STYLESHEET => 'csscompressor',
	),
	'docroot'	=> DOCROOT,
);
