<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_check_freeup()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			
			$objects_to_action[] = $object->get_row_as_obj(array("id","title"));
			$services_to_action[] = Service::factory("Up")->get();
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}

		$this->json['count'] = Service::factory("Up")->get_balance();
		$this->json['available'] = Service::factory("Up")->check_available(1);
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;

		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
		}
	}

	public function action_check_premium()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->param->params) ? (array) $this->param->params : array();

		$this->json['objects'] = $objects_to_action = ORM::factory('Object')
															->where("id","IN", $ids)
															->where("active","=",1)
															->getprepared_all(array("id","title"));

		if (count($objects_to_action) == 0) {
			$this->json['code'] = 400;
			return;
		}

		$this->json['object'] = $first_object = $objects_to_action[0];

		$this->json['services'] = array(
			'premium' => Service::factory("Premium", $first_object->id)->set_params($params)->get(),
			'email' => Service::factory("Email", $first_object->id)->set_params($params)->get()
		);

		$this->json['count'] = Service::factory("Premium")->get_balance();
		$this->json['available'] = Service::factory("Premium")->check_available(1);
	}

	public function action_check_email()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->param->params) ? (array) $this->param->params : array();

		$this->json['objects'] = $objects_to_action = ORM::factory('Object')
															->where("id","IN", $ids)
															->where("active","=",1)
															->getprepared_all(array("id","title"));

		if (count($objects_to_action) == 0) {
			$this->json['code'] = 400;
			return;
		}

		$this->json['object'] = $first_object = $objects_to_action[0];

		$this->json['services'] = array(
			'email' => Service::factory("Email", $first_object->id)->set_params($params)->get()
		);
	}

	public function action_check_tglink()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$post = $this->request->post();

		if (!isset($post['category']) OR !$post['category']) {
			$post['category'] = 'tg1';
		}

		if (!isset($post['quantity']) OR !$post['quantity']) {
			$post['quantity'] = 1;
		}

		$this->json['objects'] = $objects_to_action = ORM::factory('Object')
															->where("id","IN", $ids)
															->where("active","=",1)
															->getprepared_all(array("id","title"));

		if (count($objects_to_action) == 0) {
			$this->json['code'] = 400;
			return;
		}

		$this->json['object'] = $first_object = $objects_to_action[0];

  
		$this->json['services'] = array(
			'tglink' => Service::factory("Tglink", $first_object->id)->set_params($post)->get()
		);
	}

	public function action_check_lider()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->param->params) ? (array) $this->param->params : array();

		$this->json['objects'] = $objects_to_action = ORM::factory('Object')
															->where("id","IN", $ids)
															->where("active","=",1)
															->getprepared_all(array("id","title","main_image_id"));

		if (count($objects_to_action) == 0) {
			$this->json['code'] = 400;
			return;
		}

		$this->json['object'] = $first_object = $objects_to_action[0];

		if (!$this->json['object']->main_image_id) {
			$this->json['code'] = 400;
			$this->json['text'] = 'Загрузите хотябы одну фотографию';
		}

		$this->json['services'] = array(
			'lider' => Service::factory("Lider", $first_object->id)->set_params($params)->get(),
			'email' => Service::factory("Email", $first_object->id)->set_params($params)->get()
		);

		$this->json['count'] = Service::factory("Lider")->get_balance();
		$this->json['available'] = Service::factory("Lider")->check_available(1);
	}

	public function action_check_newspaper()
	{
		$ids = ($this->param->ids) ? $this->param->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->param->params) ? (array) $this->param->params : array();

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$object =  $object->get_row_as_obj(array("id","title"));
			$objects_to_action[] = $object;

			$service = Service::factory("Newspaper", $object->id);
			$service->set_params($params);
			$service_info = $service->get();
			
			$services_to_action[] = $service_info;
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}
		$this->json["type_info"] = Service::factory("Newspaper")->get_info();
		$this->json['count'] = 0;
		$this->json['available'] = FALSE;
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;
		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
		}
	}

	public function action_check_kupon()
	{
		$object_id = intval($this->param->id);
		$ad = ORM::factory('Object', $object_id);
		if (!$ad->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$groups = ORM::factory('Kupon_Group')->get_by_object($ad->id);
		$available_count = 0;
		$this->json['groups'] = array();		

		foreach ($groups as $group) {
			
			$service = Service::factory("Kupon", $group->id);
			$available = $service->check_available(1);

			if ($available !== TRUE) continue;
			
			$this->json['groups'][] = array(
				"id" => $group->id,
				"available" => TRUE,
				"service" => $service->get()
			);

			$available_count = $available_count + 1;
		}

		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		if ($available_count == 0)
		{
			$this->json['code'] = 400;
			$this->json['text'] = "Недоступен для заказа (отсутсвует)";
		}
	}

	public function action_check_cities()
	{

		$ids = ($this->post->ids) ? $this->post->ids: array($this->param->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->param->params) ? (array) $this->param->params : array();

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$object =  $object->get_row_as_obj(array("id","title"));
			$objects_to_action[] = $object;

			$service = Service::factory("Cities", $object->id);
			$service->set_params($params);
			$service_info = $service->get();
			
			$services_to_action[] = $service_info;
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}
		$this->json["cities_info"] = Service::factory("Cities", $object->id)->get_info();
		$this->json['count'] = 0;
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;
		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
		}
	}

	

	public function action_save()
	{
		if (!isset($this->post->serviceData["result"]) OR !isset($this->post->serviceData["info"])) {
			$this->json['code'] = 400;
			$this->json['text'] = "Ошибка при сохранении услуги. Отсутствие обязательных параметров";
			return;
		}
		
		$key = Cart::get_key();
		$order = ORM::factory('Order')
					->where("key","=",$key)
					->where("state","=",0)
					->find();
		$info = new Obj($this->post->serviceData["info"]);
		$result = new Obj($this->post->serviceData["result"]);
		$tempOrderItemId = ( isset($this->post->serviceData["temp_order_item_id"]) ) ? $this->post->serviceData["temp_order_item_id"] : NULL;

		$db = Database::instance();
		try {
			$db->begin();

			$objects = $info->objects;
			if ($objects) {
				$services = $info->services;
				unset($info->objects);
				unset($info->services);
				foreach ($objects as $k => $object_info) {


					$service_info = $services[$k];
					$service_name = Text::ucfirst($service_info["name"]);
					// if (in_array($service_info->service["name"], array("premium","up"))) {
					// 	$service_info->count = Service::factory($service_name)->get_balance();
					// 	$service_info->available = Service::factory($service_name)->check_available(1);
					// }
					$service = Service::factory($service_name, $object_info['id']);
					$service->set_params($result);
					$orderItemTemp	=  $service->save($object_info, $key, $tempOrderItemId, $order->id);
				}
			} else {
				$service_name = Text::ucfirst($info->service["name"]);
				$service = Service::factory($service_name, $info->id);
				$service->set_params($result);
				$orderItemTemp = $service->save($info->object, $key, $tempOrderItemId, $order->id);
			}

			
			
			$db->commit();
		} catch (Kohana_Exception $e) {
			$db->rollback();
			$this->json["text"] = "Ошибка при сохранении услуги. ".$e->getMessage();
			$this->json["code"] = 400;
			return;
		}
		$this->json["result"] = $orderItemTemp;
	}

	public function action_save_service()
	{
		if (!isset($this->post->serviceData["result"]) OR !isset($this->post->serviceData["info"])) {
			$this->json['code'] = 400;
			$this->json['text'] = "Ошибка при сохранении услуги. Отсутствие обязательных параметров";
			return;
		}
		
		$key = Cart::get_key();
		$order = ORM::factory('Order')
					->where("key","=",$key)
					->where("state","=",0)
					->find();
		$info = $this->post->serviceData["info"];
		$result = $this->post->serviceData["result"];
		$tempOrderItemId = ( isset($this->post->serviceData["temp_order_item_id"]) ) ? $this->post->serviceData["temp_order_item_id"] : NULL;

		$db = Database::instance();
		try {
			$db->begin();

			$objects = $info['objects'];

			foreach ($info['objects'] as $k => $object) {

				if (isset($result['premium']) AND $result['premium']['quantity'] > 0) {

					$service = Service::factory('Premium', $object['id']);
					$service->set_params($result['premium']);

					$orderItemTemp	=  $service->save($object, $key, $tempOrderItemId, $order->id);

				}

				if (isset($result['email']) AND $result['email']['quantity'] > 0) {

					$service = Service::factory('Email', $object['id']);
					$service->set_params($result['email']);

					$orderItemTemp	=  $service->save($object, $key, $tempOrderItemId, $order->id);
					
				}

				if (isset($result['lider']) AND $result['lider']['quantity'] > 0) {

					$service = Service::factory('Lider', $object['id']);
					$service->set_params($result['lider']);

					$orderItemTemp	=  $service->save($object, $key, $tempOrderItemId, $order->id);
					
				}

				if (isset($result['tglink']) AND $result['tglink']['quantity'] > 0) {

					$service = Service::factory('Tglink', $object['id']);
					$service->set_params($result['tglink']);

					$orderItemTemp	=  $service->save($object, $key, $tempOrderItemId, $order->id);
					
				}

			}

			$db->commit();

		} catch (Kohana_Exception $e) {
			$db->rollback();
			$this->json["text"] = "Ошибка при сохранении услуги. ".$e->getMessage();
			$this->json["code"] = 400;
			return;
		}
		$this->json["result"] = $orderItemTemp;
	}

	public function action_cart_count()
	{
		$this->json = array_merge($this->json, Cart::get_info());
	}

	public function action_get_temp_item()
	{
		$id = (int) $this->get->id;
		$item = ORM::factory('Order_ItemTemp', $id);
		if ($item->loaded())
		{
			$item = $item->get_row_as_obj();
			$item->params = json_decode($item->params);
			$this->json["result"] = $item;
			return;
		}

		$this->json["code"] = 400;
	}

	public function action_check_kupon_number()
	{
		$number = $this->post->number;
		$captcha = $this->post->captcha;

		$number = preg_replace('/[^0-9]/', '', $number);
		if (!$number) {
			throw new HTTP_Exception_404;
		}

		$number = " ".$number;
		$crypt_number = Model_Kupon::crypt_number($number);

		$REMOTE_ADDR = URL::SERVER("REMOTE_ADDR");
		$token = "check_kupon_number:{$REMOTE_ADDR}";
		
		$shows_counter = (Cache::instance("memcache")->get($token)) ? Cache::instance("memcache")->get($token) : 0;

		$shows_counter = $shows_counter + 1;
		Cache::instance("memcache")->set($token, $shows_counter, Date::MINUTE);
		if ($shows_counter > 0)
		{

			$twig = Twig::factory('block/captcha/check_number');
			
			if (isset($captcha)) {
				$validation = Validation::factory(array("captcha" => $captcha))
						->rule('captcha', 'not_empty', array(':value', ""))
						->rule('captcha', 'captcha', array(':value', ""));
				if ( !$validation->check())
				{

					$twig->error = "Не правильный код";
					$this->json["code"] = 300;
					$twig->captcha = Captcha::instance()->render();
					$this->json["result"] = (string) $twig;
				} else {
					$kupon = ORM::factory('Kupon')
								->where("number","=",$crypt_number)
								->find();

					if ($kupon->loaded()) {
						$this->json["code"] = 200;
					} else {
						$this->json["code"] = 400;
						$this->json["error"] = "Купон отсуствует1";
					}

					Cache::instance("memcache")->delete($token);
				}
			} else {
				$twig->error = "Введите капчу";
				$this->json["code"] = 300;
				$twig->captcha = Captcha::instance()->render();
				$this->json["result"] = (string) $twig;
			}
		} else {
			$kupon = ORM::factory('Kupon')
						->where("number","=",$crypt_number)
						->find();

			if ($kupon->loaded()) {
				$this->json["code"] = 200;
			} else {
				$this->json["code"] = 400;
				$this->json["error"] = "Купон отсуствует2";
			}
		}
		
		
		
		
	}

	public function action_get_prices()
	{


		$category_id = $this->request->post('category_id');
		$city_id = $this->request->post('city_id');

		$params = array(
			'category' => ORM::factory('Category')->where('id','=',$category_id)->cached(Date::WEEK)->find()->seo_name,
			'city' => ORM::factory('City')->where('id','=',$city_id)->cached(Date::WEEK)->find()->seo_name,
			'quantity' => 1
		);


		$premium = Service::factory("Premium");
		$premium->set_params($params);
		$this->json['premium'] = $premium->getPriceMultiple();

		$lider = Service::factory("Lider");
		$lider->set_params($params);
		$this->json['lider'] = $lider->getPriceMultiple();

	}
}
