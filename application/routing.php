<?php defined('SYSPATH') or die('No direct script access.');

// close admin controllers
Route::set('admin', '<controller>(/<action>)', array('controller' => '(admin_.*|Admin_.*)'))
	->filter(function($route, $params, $request){
	throw new HTTP_Exception_404;
});

Route::set('add', 'add/<rubricid>', array('rubricid' => '.*[0-9]'))
	->defaults(array(
		'controller' => 'Add',
		'action'     => 'index',
	));

Route::set('/','')
	->filter(function($route, $params, $request){
		$domain = $_SERVER['HTTP_HOST'];
		$config = Kohana::$config->load("common");
		$main_domain = $config["main_domain"];
		if ($domain <> $main_domain) {
			$params['controller'] = 'Index';
			$params['action'] = 'city';
		} else {
			$params['controller'] = 'Index';
			$params['action'] = 'index';
		}
		return $params;
	})
	->defaults(array(
		'controller' => 'Index',
		'action'     => 'index'
	));

Route::set('detail', '<path>/<object_seo_name>.html',  array('path' => '[a-zA-Z0-9-_/]+'))
	->defaults(array(
		'controller' => 'Detail',
		'action'     => 'index'
	));

Route::set('object_edit', 'user/edit_ad/<object_id>')
	->defaults(array(
		'controller' => 'User',
		'action'     => 'edit_ad',
	));
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
Route::set('ajax_massload', 'ajax/massload/<action>')
	->defaults(array(
		'controller' => 'Ajax_Massload',
		'action'     => 'index',
	));
Route::set('ajax_landing', 'ajax/landing/<action>')
	->defaults(array(
		'controller' => 'Ajax_Landing',
		'action'     => 'index',
	));
Route::set('ajax_contacts', 'ajax/object_contacts')
	->defaults(array(
		'controller' => 'Ajax',
		'action'     => 'object_contacts',
	));
Route::set('ajax_admin', 'ajax/admin/<action>')
	->defaults(array(
		'controller' => 'Ajax_Admin',
		'action'     => 'index',
	));
Route::set('userpage', 'users/<login>')
	->defaults(array(
		'controller' => 'User',
		'action'     => 'userpage',
	));
Route::set('massload_conformities', 'user/massload_conformities/<category>(/<user_id>)')
	->defaults(array(
		'controller' => 'User',
		'action'     => 'massload_conformities',
	));
Route::set('article', 'article/<seo_name>')
	->defaults(array(
		'controller' => 'Article',
		'action'     => 'index',
	));

Route::set('ourservices', 'ourservices/<seo_name>')
	->defaults(array(
		'controller' => 'Article',
		'action'     => 'ourservices',
	));

Route::set('newsone', 'news/<id>-<seo_name>', array('id' => '\d+'))
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

Route::set('backend/reklama/linkstat', 'khbackend/reklama/linkstat/<id>', array('id' => '\d+'))
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Reklama',
		'action'     => 'linkstat',
	));

Route::set('backend/reklama/menubannerstat', 'khbackend/reklama/menubannerstat/<id>', array('id' => '\d+'))
	->defaults(array(
		'directory'  => 'Admin',
		'controller' => 'Reklama',
		'action'     => 'menubannerstat',
	));

Route::set('global_search', 'ajax/global_search')
	->defaults(array(
		'controller' => 'Ajax',
		'action'     => 'global_search',
	));

Route::set('redirect/ref_cb', 'redirect/ref_cb')
	->defaults(array(
		'controller' => 'Redirect',
		'action'     => 'ref_cb',
	));

Route::set('cart', 'cart')
	->defaults(array(
		'controller' => 'Cart',
		'action'     => 'index',
	));

if (array_key_exists("HTTP_FROM", $_SERVER))
{
	if (strpos($_SERVER['REQUEST_URI'],"landing"))
	{
		Route::set('landing', 'landing(/<domain>(/<action>(/<id>)))')
		->defaults(array(
			'controller' => 'landing',
			'action'     => 'index',
		));
	} else {
		Route::set('/', '<action>(/<id>)')
		->defaults(array(
			'controller' => 'landing',
			'action'     => 'index',
		));
	}
} else {
	Route::set('landing', 'landing(/<domain>(/<action>(/<id>)))')
		->defaults(array(
			'controller' => 'landing',
			'action'     => 'index',
		));
}
/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>(/<other1>(/<other2>(/<other3>(/<other4>(/<other5>))))))))')
	->filter(function($route, $params, $request){
		$category = ORM::factory('Category')
			->where("seo_name","=", strtolower($params["controller"]) )
			->find();
		if ($category->loaded()) {
			$config = Kohana::$config->load("landing");
			$config = $config["categories"];
			if ( in_array( $category->seo_name,  array_keys((array) $config)) 
					AND $params["action"] == "index") {
				$params['controller'] = $config[$category->seo_name][0];
				$params['action'] = $config[$category->seo_name][1];
			} else {
				$params['controller'] = 'Search';
				$params['action'] = 'index';
			}
			return $params;

		}
	})
	->defaults(array(
		'controller' => 'Index',
		'action'     => 'index',
	));
