<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Settings extends Controller_Admin_Template {

	protected $module_name = 'settings';

	function action_index()
	{
		if (HTTP_Request::POST === $this->request->method()) 
		{
			$email = $this->request->post("email");

			setcookie('user_id', '', time()-1, '/', Region::get_cookie_domain());
			Auth::instance()->logout();

			$user = ORM::factory('User');
			$user->get_user_by_email($email)->find();

			Auth::instance()->trueforcelogin($user);

			$this->redirect('user/published');
		}
	}

	function action_cache()
	{
		$this->template->tags = Kohana::$config->load("cache.memcache_tags");
	}

	function action_memcache_reset()
	{
		$this->auto_render = FALSE;

		$tag = $this->request->param('id');

		ORM::factory('Memcached')
			->where("expired","<","NOW()")
			->delete_all();

		$memcache_list = ORM::factory('Memcached')
						->where("tag","LIKE","%$tag%")
						->find_all();
		foreach ($memcache_list as $memcache) {
			Cache::instance()->set($memcache->key, NULL, 0);

			ORM::factory('Memcached', $memcache->id)->delete();
		}

		$this->redirect('/khbackend/settings/cache');
	}

}