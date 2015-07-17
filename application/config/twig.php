<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'functions' => array(
		'domain'  		=> array ('func' => array('Twig_Functions', 'domain'), 'options' => array() ),
		'debug'  		=> array ('func' => array('Twig_Functions', 'debug'), 'options' => array() ),
		'obj'  		=> array ('func' => array('Twig_Functions', 'obj'), 'options' => array() ),
		'requestblock'  => array ('func' => array('Twig_Functions', 'requestblock'), 'options' => array() ),
		'requestview'  => array ('func' => array('Twig_Functions', 'requestview'), 'options' => array() ),
		'css' 			=> array ('func' => array('Twig_Functions', 'css'), 'options' => array() ),
		'js' 			=> array ('func' => array('Twig_Functions', 'js'), 'options' => array() ),
		'url' 			=> array ('func' => array('Twig_Functions', 'url'), 'options' => array() ),
		'staticfile' 	=> array ('func' => array('Twig_Functions', 'staticfile'), 'options' => array() )
	),
	'filters' => array(
		'contacthide' => array('Twig_Filters', 'contacthide'),
	),
);