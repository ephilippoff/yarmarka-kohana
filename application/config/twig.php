<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'functions' => array(
		'debug'  		=> array ('func' => array('Twig_Functions', 'debug'), 'options' => array() ),
		'requestblock'  => array ('func' => array('Twig_Functions', 'requestblock'), 'options' => array() ),
		'requestview'  => array ('func' => array('Twig_Functions', 'requestview'), 'options' => array() ),
		'css' 			=> array ('func' => array('Twig_Functions', 'css'), 'options' => array() ),
		'js' 			=> array ('func' => array('Twig_Functions', 'js'), 'options' => array() ),
		'url' 			=> array ('func' => array('Twig_Functions', 'url'), 'options' => array() ),
		'staticfile' 	=> array ('func' => array('Twig_Functions', 'staticfile'), 'options' => array() )
	),
);