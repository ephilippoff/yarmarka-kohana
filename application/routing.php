<?php defined('SYSPATH') or die('No direct script access.');

// close admin controllers
Route::set('admin', '<controller>(/<action>)', array('controller' => '(admin_.*|Admin_.*)'))->filter(function($route, $params, $request){
	throw new HTTP_Exception_404;
});
// backend routing
Route::set('backend', 'khbackend(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Users',
		'action'     => 'index',
	));
/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));
