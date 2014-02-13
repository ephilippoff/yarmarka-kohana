<?php defined('SYSPATH') or die('No direct script access.');

// close admin controllers
Route::set('admin', '<controller>(/<action>)', array('controller' => '(admin_.*|Admin_.*)'))->filter(function($route, $params, $request){
	throw new HTTP_Exception_404;
});

// @todo дикий костыль благодаря админам
if (isset($_SERVER['HTTP_HOST']) AND $_SERVER['HTTP_HOST'] == 'c.yarmarka.biz')
{
	Route::set('master_save_object', 'add/master_save_object')
		->defaults(array(
			'controller' => 'Add',
			'action'     => 'save_object',
		));
}
Route::set('not_unique_contact_msg', 'block/not_unique_contact_msg/<number>')
	->defaults(array(
		'controller' => 'Block',
		'action'     => 'not_unique_contact_msg',
	));
Route::set('ajax_link_to_company', 'ajax/link_user/<login>', array('login' => '.*'))
	->defaults(array(
		'controller' => 'Ajax',
		'action'     => 'link_user',
	));
Route::set('userpage', 'users/<login>')
	->defaults(array(
		'controller' => 'User',
		'action'     => 'userpage',
	));
Route::set('article', 'article/<seo_name>')
	->defaults(array(
		'controller' => 'Article',
		'action'     => 'index',
	));

Route::set('newsone', 'news/<seo_name>')
	->defaults(array(
		'controller' => 'Article',
		'action'     => 'newsone',
	));

Route::set('backend/news', 'articles/news')
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Articles',
		'action'     => 'news',
	));

Route::set('backend/ip_info', 'khbackend/users/ip_info/<ip>', array('ip' => '.*'))
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Users',
		'action'     => 'ip_info',
	));
	
// backend default routing
Route::set('backend', 'khbackend(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Welcome',
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
