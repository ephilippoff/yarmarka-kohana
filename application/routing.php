<?php defined('SYSPATH') or die('No direct script access.');

// close admin controllers
Route::set('admin', '<controller>(/<action>)', array('controller' => '(admin_.*|Admin_.*)'))
	->filter(function($route, $params, $request){
	throw new HTTP_Exception_404;
});

Route::set('robots', 'robots.txt')
	->defaults(array(
		'controller' => 'Static',
		'action'     => 'robots',
	));

Route::set('sitemaps', 'sitemaps/index.xml')
	->defaults(array(
		'controller' => 'Static',
		'action'     => 'sitemaps',
	));

Route::set('all','<addr>', array('addr' => '.*'))
	->filter(function($route, $params, $request){
		$site_disable = Kohana::$config->load("common.site_disable");
		if ($site_disable) {
			$white_ips = Kohana::$config->load("common.white_ips");
			if (!in_array($_SERVER['REMOTE_ADDR'], $white_ips)) {
				$params['controller'] = 'Redirect';
				$params['action'] = 'maitenance';
				return $params;
			} else {
				$_SESSION["site_disable"] = TRUE;
				return FALSE;
			}
		}
		$_SESSION["site_disable"] = FALSE;
		return FALSE;
	});

Route::set('object_edit', 'edit/<object_id>', array('object_id' => '.*[0-9]'))
	->defaults(array(
		'controller' => 'Add',
		'action'     => 'edit_ad',
	));

Route::set('add', 'add/<rubricid>', array('rubricid' => '.*[0-9]'))
	->defaults(array(
		'controller' => 'Add',
		'action'     => 'index',
	));

Route::set('/','')
	->filter(function($route, $params, $request){
		$domain = $_SERVER['HTTP_HOST'];
		$domain_segments = explode(".", $domain);
		$config = Kohana::$config->load("common");
		$main_domain = $config["main_domain"];
		$main_category = $config["main_category"];
		//if ($domain <> $main_domain || $request->query("search")) {
		if ($request->query("search")) {
			$config = Kohana::$config->load("landing");
			$config = $config["categories"];
			if ( in_array( $domain_segments[0],  array_keys((array) $config))) {
				$params['controller'] = $config[$domain_segments[0]][0];
				$params['action'] = $config[$domain_segments[0]][1];
			} else {
				$params['controller'] = 'Search';
				$params['action'] = 'index';
			}
			$params['category_path'] = $main_category;
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

Route::set('detail_work', 'detail/<object_id>',  array('object_id' => '[0-9]+'))
	->filter(function($route, $params, $request){
		$params["is_old"] = TRUE;
		return $params;
	})
	->defaults(array(
		'controller' => 'Detail',
		'action'     => 'index'
	));

Route::set('detail', '<path>/<object_seo_name>.html',  array('path' => '[a-zA-Z0-9-\._/]+'))
	->filter(function($route, $params, $request){

		$object_seo_name =  $params["object_seo_name"];
		$object_category_segment = trim($params["path"], "/");
		$object_seo_name_segments = explode("-", $object_seo_name);

		$object_id =  (int) end($object_seo_name_segments);

		if ($object_id == 0) {
			return FALSE;
		}

		$object = ORM::factory('Objectcompiled', $object_id);
		if (!$object->loaded()) {
			return FALSE;
		}

		$url = $object->get_full_url();
		$params["url"] = $url;
		$params["object"] = $object;

		if ($object->type_tr AND in_array($object->type_tr, array(101,102,201,89))) {
			$params["action"] = "type".$object->type_tr;
		}

		return $params;
	})->defaults(array(
		'controller' => 'Detail',
		'action'     => 'index'
	));

/**
 * Поддержка старых урлов на карточку
 */
// Route::set('detail_old', '<path>',  array('path' => '[a-zA-Z0-9-_/]+'))
// 	->filter(function($route, $params, $request){
// 		$segments = explode("-", $params["path"]);
// 		$maybe_id = (int) end($segments);

// 		$segments2 = explode("/", $params["path"]);
// 		$maybe_id2 = (int) end($segments2);

// 		if (($maybe_id > 0 AND $maybe_id < 9999999) OR ($maybe_id2 > 0 AND $maybe_id2 < 9999999)) {
// 			$params["is_old"] = TRUE;
// 			$params["object_id"] = ($maybe_id) ? $maybe_id : $maybe_id2;
// 			return $params;
// 		} else {
// 			return FALSE;
// 		}
// 	})->defaults(array(
// 		'controller' => 'Detail',
// 		'action'     => 'index'
// 	));

Route::set('reklamodatelyam_static', 'reklamodatelyam/<path>', array('path' => '.*(\.js|\.css|\.png|\.jpg|\.gif)$'))
	->defaults(array(
		'controller' => 'Static',
		'action'     => 'reklamodatelyam_static',
	));
	
Route::set('reklamodatelyam', 'reklamodatelyam/<path>.html', array('path' => '.*'))
	->defaults(array(
		'controller' => 'Static',
		'action'     => 'reklamodatelyam',
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

Route::set('newsline', 'newsline/<date>', array('date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}'))
	->defaults(array(
		'controller' => 'Article',
		'action'     => 'newsline',
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

//user routing

Route::set('user', 'user(/<action>(/<category_path>))', array(
		'category_path' => '[a-zA-Z0-9-\._/]+',
	))
	->filter(function($route, $params, $request){
		if (in_array($params["action"], array("login", "logout", "registration", "account_verification","forgot_password", "forgot_password_link") ))
		{
			$params["controller"] = 'User_Auth';
			return $params;
		}
		elseif ( in_array($params["action"], array("published", "unpublished", "favorites")) )
		{
			$params["controller"] = 'User_Search';
			return $params;
		}

		elseif ( in_array($params["action"], array("userinfo", "orginfo", "password", "employers", "contacts","user_link_request", "orginfoinn_decline_user")) )
		{
			$params["controller"] = 'User_Profile';
			return $params;
		}

		elseif ( in_array($params["action"], array("orders", "subscriptions", "objectload")) )
		{
			$params["controller"] = 'User_Service';
			return $params;
		}

		return FALSE;
	})
	->defaults(array(
		'controller' => 'User_Search',
		'action'     => 'published',
	));

//end user routing


/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->filter(function($route, $params, $request){
		if (! in_array($params["controller"], Kohana_Other::get_controllers(Kohana::list_files('classes/Controller')))) {
			return FALSE;
		}
		if ($params["controller"] == 'Search') {
			return FALSE;
		}
	})
	->defaults(array(
		'controller' => 'Index',
		'action'     => 'index',
	));

Route::set('search', '<category_path>', array(
		'category_path' => '[a-zA-Z0-9-\._/]+',
	))->filter(function($route, $params, $request){
		$performance = Performance::factory(Acl::check('profiler'));
		$performance->add("SearchRouting","start");
		$segments = explode("/", $params["category_path"]);

		$city = ORM::factory('City')
			->where("seo_name","=", strtolower($segments[0]) )
			->cached(Date::WEEK)
			->find();

		if ($city->loaded() OR $segments[0] == 'tyumenskaya-oblast' OR $segments[0] == 'obyavlenie') {
			$params['controller'] = 'Redirect';
			$params['action'] = 'old_link';
			return $params;
		}

		$category = ORM::factory('Category')
			->where("seo_name","=", strtolower($segments[0]) )
			->cached(Date::WEEK)
			->find();
		
		if ($category->loaded()) {
			$config = Kohana::$config->load("landing");
			$config = $config["categories"];
			if ( in_array( $category->seo_name,  array_keys((array) $config)) 
					AND count($segments) == 1) {
				$params['controller'] = $config[$category->seo_name][0];
				$params['action'] = $config[$category->seo_name][1];
			} else {
				$params['controller'] = 'Search';
				$params['action'] = 'index';
			}
			$performance->add("SearchRouting","end");
			return $params;

		} else {
			return FALSE;
		}
	});

// Route::set('sitemap', '(<subtitle>.)sitemap.xml')
// 	->defaults(array(
// 		'controller' => 'Static',
// 		'action'     => 'sitemap',
// 	));
