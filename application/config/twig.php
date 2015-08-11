<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'functions' => array(
		'domain'  		=> array ('func' => array('Twig_Functions', 'domain'), 'options' => array() ),
		'debug'  		=> array ('func' => array('Twig_Functions', 'debug'), 'options' => array() ),
		'obj'  		=> array ('func' => array('Twig_Functions', 'obj'), 'options' => array() ),
		'requestblock'  => array ('func' => array('Twig_Functions', 'requestblock'), 'options' => array() ),
		'requestoldview'  => array ('func' => array('Twig_Functions', 'requestoldview'), 'options' => array() ),
		'css' 			=> array ('func' => array('Twig_Functions', 'css'), 'options' => array() ),
		'js' 			=> array ('func' => array('Twig_Functions', 'js'), 'options' => array() ),
		'url' 			=> array ('func' => array('Twig_Functions', 'url'), 'options' => array() ),
		'staticfile' 	=> array ('func' => array('Twig_Functions', 'staticfile'), 'options' => array() ),
		'file_exist' 	=> array ('func' => array('Twig_Functions', 'file_exist'), 'options' => array() ),
		'strim' 	=> array ('func' => array('Twig_Functions', 'strim'), 'options' => array() ),
		'check_object_access' => array ('func' => array('Twig_Functions', 'check_object_access'), 'options' => array() ),
		'get_stat_cached_info' => array ('func' => array('Twig_Functions', 'get_stat_cached_info'), 'options' => array() ),
		'check_access' => array ('func' => array('Twig_Functions', 'check_access'), 'options' => array() ),
		'get_cart_info' => array ('func' => array('Twig_Functions', 'get_cart_info'), 'options' => array() ),
		'get_favorites_info' => array ('func' => array('Twig_Functions', 'get_favorites_info'), 'options' => array() ),
		'get_myobjects_info' => array ('func' => array('Twig_Functions', 'get_myobjects_info'), 'options' => array() ),
		'get_form_element' => array ('func' => array('Twig_Functions', 'get_form_element'), 'options' => array() ),
	),
	'filters' => array(
		'contacthide' => array('Twig_Filters', 'contacthide'),
		'values' => array('Twig_Filters', 'values')
	),
);