<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'merge'      => FALSE,
	'folder'     => 'assets',
	'load_paths' => array(
		Assets::STYLESHEET => DOCROOT.'static'.DIRECTORY_SEPARATOR.'develop'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR,
		Assets::JAVASCRIPT => DOCROOT.'static'.DIRECTORY_SEPARATOR.'develop'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR,
	),
	'processor'  => array(
		Assets::STYLESHEET => 'csscompressor',
	),
	'docroot'	=> DOCROOT,
);
