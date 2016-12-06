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

	public function action_get_test_message() {
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;


		$subjects =	array(  "add_notice" => "Поздравляем Вас с успешным размещением объявления на ",
							"block_contact" => 'Сообщение от модератора сайта ',
							"contact_verification_code" => "Подтверждение email на ",
							//"fast_register_success" => "",
							"forgot_password" => "Восстановление пароля",
							"kupon_notify" => "Вы приобрели купоны на скидку",
							"manage_object" => "Сообщение от модератора сайта",
							"object_to_archive" => "Ваши объявления перемещены в архив",
							"payment_success" => "Потверждение оплаты. Заказ №",
							"payment_success_apply_notify" => "Оплата объявлений в газету",
							"register_data" => "Для вас создана учетная запись на сайте yarmarka.biz",
							"register_success" => "Подтверждение регистрации на Ярмарке");

		$subject = $this->request->param('id');

		$email = ORM::factory('Email')->where('title','LIKE','%'.$subjects[$subject].'%')->order_by("id","desc")->find();

		$json = array();

		$json['code'] = 200;
		$json['result'] = ($email->loaded()) ? $email->id: FALSE;


		$this->response->body(json_encode($json));
	}

	public function action_test_email() {

		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			if (!isset($_REQUEST['to']) OR !isset($_REQUEST['email_id'])) {
				die;
			}

			$email = ORM::factory('Email', (int) $_REQUEST['email_id']);

			if (!$email->loaded()) return;

			$toTokens = explode(',', $_REQUEST['to']);
			// $subj = '(Тест без шаблона) Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!';			
			// $msg = View::factory('emails/add_notice',
			// 		array(
			// 			'is_edit' => false,
			// 			'object' => ORM::factory('Object', 2597476), 
			// 			'name' => Auth::instance()->get_user()->get_user_name(), 
			// 			'obj' => ORM::factory('Object', 2597476), 
			// 			'city' => ORM::factory('City', 1979), 
			// 			'category' => ORM::factory('Category', 140), 
			// 			'subdomain' => Region::get_domain_by_city(1979), 
			// 			'contacts' => array(), 
			// 			'address' => 'Hoàng Quốc Việt,Phú Mỹ,7,Hồ Chí Minh, Вьетнам'
			// 		)
			// 	);

			//foreach($toTokens as $token) {
				Email::send($toTokens, Kohana::$config->load('email.default_from'), $email->title, $email->text);
			//}

		}

	}

	public function action_fix_companies() {
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		$query = DB::query(Database::SELECT, "insert into user_settings (user_id,value,name,type)
												select id,0,'moderate','orginfo' from \"user\" where org_type=2 
												and not exists (select id from user_settings where type='orginfo' and name='moderate' and user_id = \"user\".id)
												and exists (select id from user_settings where type='orginfo' and name='mail_address' and user_id = \"user\".id)", FALSE)
							->execute();
		echo "OK";
	}

}