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

	public function action_test_email() {

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			if (!isset($_REQUEST['to'])) {
				die;
			}

			$toTokens = explode(',', $_REQUEST['to']);
			$subj = 'Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!';			
			$msg = View::factory('emails/add_notice',
					array(
						'is_edit' => false,
						'object' => ORM::factory('Object', 2597476), 
						'name' => Auth::instance()->get_user()->get_user_name(), 
						'obj' => ORM::factory('Object', 2597476), 
						'city' => ORM::factory('City', 1979), 
						'category' => ORM::factory('Category', 140), 
						'subdomain' => Region::get_domain_by_city(1979), 
						'contacts' => array(), 
						'address' => 'Hoàng Quốc Việt,Phú Mỹ,7,Hồ Chí Minh, Вьетнам'
					)
				);

			foreach($toTokens as $token) {
				Email::send($token, Kohana::$config->load('email.default_from'), $subj, $msg);
			}

		}

	}

}