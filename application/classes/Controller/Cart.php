<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cart extends Controller_Template {

	protected $json = array();

	public function before()
	{
		parent::before();

		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->domain = new Domain();
		//$this->assets->css('bs_grid.css');
	}

	public function action_index()
	{

		$twig = Twig::factory('cart/index');
		$twig->user = $user = Auth::instance()->get_user();
		$twig->canonical_url = 'user/cart';
		$cart = $cartTempItems = $sale_types = array();
		$key = Cart::get_key();

		$twig->crumbs = array(
			array(
				"title" => "Корзина - оформление заказа"
			)
		);

		$twig->city = $this->domain->get_city();

		$sum = 0;
		$need_delivery = FALSE;
		//если корзина не пуста, т.е. есть ключ
		if ($key) {

			$cart_tems = ORM::factory('Order_ItemTemp')
								->where("key","=", $key)
								->order_by("id");

			$sum = Model_Order::each_item($cart_tems, function($service, $item, $model_item) use (&$cartTempItems, $key, &$need_delivery) {

				if (!$need_delivery)
				{
					$need_delivery = Kohana::$config->load("billing.".$item->service->name.".delivery_type");
				}
				
				//если заказан товар , проверяем доступность
				if ($item->service->name == "kupon") {
					$kupons = ORM::factory('Kupon')
								->where("id","IN",$item->service->ids)
								->find_all();
					foreach ($kupons as $kupon) {
						$item->available = $kupon->check_and_restore_reserve_if_possible($key);
						$item->avail_count = $kupon->get_avail_count($kupon->kupon_group_id);
						if ($item->available == FALSE) break;
					}
				} 

				array_push($cartTempItems, $item);
				return $item;
			});

			$twig->order = null;
			//если уже был сохранен заказ
			$order = ORM::factory('Order')->where("key","=",$key)->find();
			if ($order->loaded()) {
				$twig->order = $order;
			}
		}

		

		if ($need_delivery) {
			if ($need_delivery == "electronic") {
				$twig->next_page = "cart/electronic_delivery/";
			} else {
				$twig->next_page = "cart/delivery/";
			}
			
		} else {
			$twig->next_page = "cart/order/";
		}
		

		$twig->cartTempItems = $cartTempItems;
		$twig->sum = $sum;
		
		$this->response->body($twig);
	}

	public function action_electronic_delivery()
	{
		$twig = Twig::factory('cart/electronic_delivery');
		$twig->city = $this->domain->get_city();
		$id = $this->request->param('id');
		$twig->user = $user = Auth::instance()->get_user();
		$errors = array();
		$key = Cart::get_key();

		if (!$id) {
			HTTP::redirect("/cart");
			return;
		}

		if ($user) {
			$order = ORM::factory('Order')
					->where("user_id","=", $user->id)
					->where("id","=", intval($id))
					->find();
		} else {
			$order = ORM::factory('Order')
					->where("key","=", $key)
					->where("id","=", intval($id))
					->find();
		}
		
		if (!$order->loaded() or $order->state > 0) {
			throw new HTTP_Exception_404;
			return;
		}
		$twig->order = $order;

		$twig->crumbs = array(
			array(
				"title" => "Корзина - оформление заказа",
				"url" => "cart"
			),
			array(
				"title" => "Оформление доставки - электронная доставка"
			)
		);

		$twig->canonical_url = 'user/cart';
		
		$params = ($order->params) ? $order->params : "{}";
		$params = new Obj(json_decode($params));
		if ($params->delivery)
		{
			$twig->post = $params->delivery;
		}

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		if ($is_post)
		{

			$validation = Validation::factory((array) $this->request->post())
					->rule('email', 'not_empty', array(':value', "E-mail"))
					->rule('email', 'is_email_contact', array(':value', "E-mail"))
					->rule('name', 'not_empty', array(':value', "Имя"))
					->rule('phone', 'not_empty', array(':value', "Телефон"));

			if ( !$validation->check())
			{
				$errors = $validation->errors('validation/object_form');
			}

			$twig->errors = $errors;
			$twig->post = $this->request->post();

			if (!count($errors))
			{
				$params->delivery = array(
					"type" => "electronic",
					"name" => $this->request->post("name"),
					"email" => $this->request->post("email"),
					"phone" => $this->request->post("phone")
				);
				$order->params = json_encode((array) $params);
				$order->save();
				HTTP::redirect("/cart/order/".$order->id);
			}
		}

		$this->response->body($twig);
	}

	public function action_delivery()
	{

		$twig = Twig::factory('cart/delivery');
		$twig->city = $this->domain->get_city();
		$id = $this->request->param('id');
		$user = Auth::instance()->get_user();
		$errors = array();
		$key = Cart::get_key();

		if (!$id) {
			HTTP::redirect("/cart");
			return;
		}

		if ($user) {
			$order = ORM::factory('Order')
					->where("user_id","=", $user->id)
					->where("id","=", intval($id))
					->find();
		} else {
			$order = ORM::factory('Order')
					->where("key","=", $key)
					->where("id","=", intval($id))
					->find();
		}
		
		if (!$order->loaded() or $order->state > 0) {
			throw new HTTP_Exception_404;
			return;
		}
		$twig->order = $order;


		$twig->user_city_id = $this->domain->get_city()->id;

		
		$twig->cities = array(
			1919 => "Тюмень",
			1979 => "Сургут",
			1948 => "Нижневартовск"
		);

		$twig->crumbs = array(
			array(
				"title" => "Оформление заказа",
				"url" => "cart"
			),
			array(
				"title" => "Оформление доставки"
			)
		);

		
		$params = ($order->params) ? $order->params : "{}";
		$params = new Obj(json_decode($params));
		if ($params->delivery)
		{
			$twig->post = $params->delivery;
		}

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		if ($is_post)
		{
			if ($this->request->post("type") == "city-delivery")
			{
			$validation = Validation::factory((array) $this->request->post())
					->rule('type', 'not_empty', array(':value', "Варианты доставки"))
					->rule('address', 'not_empty', array(':value', "Адрес"))
					->rule('phone', 'not_empty', array(':value', "Телефон"))
					->rule('city', 'not_empty', array(':value', "Город"));
			} 
			else 
			{
				$validation = Validation::factory((array) $this->request->post())
					->rule('type', 'not_empty', array(':value', "Варианты доставки"));
			}
			if ( !$validation->check())
			{
				$errors = $validation->errors('validation/object_form');
			}

			$twig->errors = $errors;
			$twig->post = $this->request->post();

			if (!count($errors))
			{
				
				if ($this->request->post("type") == "city-delivery")
				{
					$params->delivery = array(
						"type" => $this->request->post("type"),
						"city" => $this->request->post("city"),
						"comment" => $this->request->post("comment"),
						"address" => $this->request->post("address"),
						"phone" => $this->request->post("phone"),
						"price" => 0
					);
				} 
				else 
				{
					$params->delivery = array(
						"type" => $this->request->post("type")
					);
				}
				$order->params = json_encode((array) $params);
				$order->save();
				HTTP::redirect("/cart/order/".$order->id);
			}
		}

		$this->response->body($twig);
	}


	public function action_order()
	{
		$twig = Twig::factory('cart/order');
		$twig->city = $this->domain->get_city();

		$id = $this->request->param('id');
		$user = Auth::instance()->get_user();
		$twig->user = $user;
		$session = Session::instance();
		$errors = $session->get("errors");
		$errors_post = $session->get("post");
		$session->delete("errors");
		$session->delete("post");

		if (!$id) {
			throw new HTTP_Exception_404;
			return;
		}

		$key = Cart::get_key();

		if (!$id) {
			HTTP::redirect("/cart");
			return;
		}

		if ($user) {
			if (Acl::check("order")) {
				$order = ORM::factory('Order')
						->where("id","=", intval($id))
						->find();
			} else {
				$order = ORM::factory('Order')
						->where("user_id","=", $user->id)
						->where("id","=", intval($id))
						->find();
			}
			
		} else {
			$order = ORM::factory('Order')
					->where("key","=", $key)
					->where("id","=", intval($id))
					->find();
		}

		if (!$order->loaded()) {
			throw new HTTP_Exception_404;
			return;
		}

		$twig->crumbs = array(
			array(
				"title" => "Мой кабинет",
				"url" => "user"
			),
			array(
				"title" => "Корзина - оформление заказа",
				"url" => "cart"
			)
		);

		$params = ($order->params) ? $order->params : "{}";
		$params = new Obj(json_decode($params));
		if ($params->delivery)
		{

			$twig->crumbs[] = array(
				"title" => "Оформление доставки",
				"url" => "cart/".$params->delivery->type."_delivery/".$order->id
			);

			$twig->delivery_info = $params->delivery;
		} else {
			if ($params->need_delivery) {
				$delivery = Kohana::$config->load("billing.kupon.delivery_type");
				if ($delivery) {
					HTTP::redirect("/cart/".$delivery."_delivery/".$order->id);
				}
			}
		}

		$twig->crumbs[] = array(
			"title" => "Подтверждение заказа"
		);

		$order_tems = ORM::factory('Order_Item')->where("order_id","=", $order->id)->order_by("id");

		$orderItems = array();
		$twig->sum = Model_Order::each_item($order_tems, function($service, $item, $model_item) use (&$orderItems) {
			$orderItems[] = $item;
			return $item;
		});

		$state = "initial";

		$state = Model_Order::get_state($order->state, TRUE);
		if ($state <> "initial")
		{
			$twig->crumbs = array(
				array(
					"title" => "Мой кабинет",
					"url" => "user"
				),
				array(
					"title" => "Корзина - оформление заказа",
					"url" => "cart"
				),
				array(
					"title" => "Подтверждение заказа",
					"url" => "cart/order/".$order->id
				),
				array(
					"title" => "Подтверждение заказа №".$order->id
				)
			);
		}

		$twig->state = $state;
		$twig->order = $order;
		$twig->orderItems = $orderItems;

		$twig->errors = $errors;
		$twig->errors_post = new Obj(($errors_post)?$errors_post:array());

		$this->response->body($twig);
	}

	public function action_saveorder()
	{
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			//throw new HTTP_Exception_404;
		}

		$user = Auth::instance()->get_user();

		// if (!$user) {
		// 	$this->json["message"] = "Требуется авторизация";
		// 	$this->json["code"] = 403;
		// 	$this->json_response();
		// 	return;
		// }

		try {
			$request = json_decode($this->request->body());
		} catch (Kohana_Exception $e) {
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}

		$key = Cart::get_key();

		if (!$key) {
			$this->json["message"] = "Неверный ключ";
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}

		$tempItemsCount = ORM::factory('Order_ItemTemp')
								->where("key", "=", $key )
								->count_all();
		if ($tempItemsCount <= 0) {
			$this->json["message"] = "Заказ пуст";
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}
		$order_id = -1;

		try {
			$params = json_decode($this->request->body());
		} catch (Exception $e) {
			$params = array();
		}

		$db = Database::instance();

		try {

			$db->begin();

			$order = ORM::factory('Order')
						->where("key","=",$key)
						->where("state","=",0)
						->find();
			$order_loaded = $order->loaded();

			$order->key = $key;
			$order->user_id = ($user) ? $user->id : NULL;
			$order->state = 0;
			$order->sum = 0;
			$order->params = ($order->params) ? $order->params : "{}";
			$order->save();

			$order_id = $order->id;

			//return balance of goods if edit cart
			if ($order_loaded)
			{
				ORM::factory('Order_Item')
						->where("order_id","=",$order_id)
						->delete_all();
			}

			$cart_tems = ORM::factory('Order_ItemTemp')
									->where("key", "=", $key)
									->order_by("id");
			
			$orderItems = array();
			$error = FALSE;
			$need_delivery = FALSE;
			$sum = Model_Order::each_item($cart_tems, function($service, $item, $model_item) use (&$orderItems, $order_id, $order_loaded, $key, &$error, $params, &$need_delivery) {

				if (!$need_delivery)
				{
					$need_delivery = Kohana::$config->load("billing.".$item->service->name.".delivery_type");
				}

				if ($item->service->name == "kupon") {
					$own_kupons = 0;

					$kupons = ORM::factory('Kupon')
								->where("id","IN",$item->service->ids)
								->find_all();
					
					foreach ($kupons as $kupon) {
						$kupon_is_mine = $kupon->check_and_restore_reserve_if_possible($key);
						$own_kupons = $own_kupons +1;
					}

					$available = FALSE;
					$avail_count = (int) ORM::factory('Kupon_Group', $item->service->group_id)->get_balance() + $own_kupons;
					
					//проверка наличия количества заказаных купонов на складе
					$quantity = Arr::get((array) $params,"quantity".$item->service->group_id, 1);
					if ($avail_count >= $quantity) {
						$available = TRUE;
					}

					//если заказанного количества нет на складе выдаем ошибку
					if ($available !== TRUE)
					{
						$error = array(
							"message" => "Указанное количество купонов превышает их остаток, измените количество в меньшую сторону",
							"code" => 400
						);
						return $item;
					}

					$in_reserve = TRUE;
					//проверка купонов в резерве, вдруг проданы уже
					if (!$model_item->check_reserve()) {
						$in_reserve = FALSE;
					}

					if ($in_reserve !== TRUE)
					{
						$error = array(
							"message" => "Ошибка при оформлении заказа, возможно купоны в корзине уже проданы. Удалите купоны из корзины, добавьте их повторно, и повторите оформление заказа.",
							"code" => 400
						);
						return $item;
					}

					if ($quantity <> $item->service->quantity) {
						$model_item->return_reserve();
						$service = Service::factory("Kupon", $item->service->group_id);
						$service->set_params(array("quantity" => $quantity));
						$item->service = new Obj($service->get());
						$model_item->save_service_params($item->service);
						$model_item->reserve($key);
					}

				} 


				if ($item->service->name == "kupon" AND $item->groups)
				{
					unset($item->groups);
				}
				
				$realItem = ORM::factory('Order_Item');
				$realItem->order_id = $order_id;
				$realItem->object_id = $item->object->id;
				$realItem->params = json_encode((array) $item);
				$realItem->service_name = $item->service->name;
				$realItem->save();

				
				$orderItems[] = $item;
				return $item;
			});

			if ($error) 
			{
				$db->rollback();
				$this->json = array_merge($this->json, $error);
				$this->json_response();
				return;
			}

			$params = json_decode($order->params);
			$params->is_surgut =  TRUE;
			if ($need_delivery) {
				$params->need_delivery =  TRUE;
			} else {
				$params->need_delivery =  FALSE;
			}
			$order->params = json_encode($params);
			$order->sum = $sum;
			$order->save();

			$db->commit();

		} catch (Kohana_Exception $e) {
			$db->rollback();
			$this->json["message"] = "Ошибка сохранения заказа. ".$e->getMessage();
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}

		if ($order_id > 0) {
			$this->json["order_id"] = $order_id;
			$this->json["code"] = 200;
		} else {
			$this->json["code"] = 400;
		}

		$goods_for_free = 0;
		$emails_for_free = 0;
		$counter = 0;
		foreach ($orderItems as $item) {
			if (in_array($item->service->discount_name, array("prepayed_premium", "free_up", "free_advert","prepayed_lider") )) {
				$goods_for_free += 1;
			}
			if (in_array($item->service->discount_name, array("free_email") )) {
				$emails_for_free += 1;
			}
			$counter += 1;
		}

		if ($emails_for_free > 0 AND $sum == 0)  {
			$this->json["message"] = "Бесплатную услугу 'E-mail - маркетинг' возможно получить только при не нулевой сумме заказа";
			$this->json["code"] = 400;
		} else if ($goods_for_free == $counter AND $sum == 0) {
			$order->fake_command(100, 22);
			$this->json["code"] = 300;
		}


		$this->json_response();

	}


	public function action_remove_item()
	{
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			//throw new HTTP_Exception_404;
		}

		$id = $this->request->param('id');
		$key = Cart::get_key();

		if ($key) {
			$items = ORM::factory('Order_ItemTemp')
				->where("key","=", $key)
				->where("id","=", $id)->find_all();

			foreach ($items as $item) {
				$item->return_reserve();
				$item->delete();
			}
		}

		$this->json["code"] = 200;
		$this->json_response();
	}

	public function action_to_payment_system()
	{

		$errors = array();

		$user = Auth::instance()->get_user();
		$order_id = $this->request->post("id");

		$order = ORM::factory('Order', $order_id);


		if ($order->state == 3) {
			$errors["cancelled_order"] = "Заказ отменен";
			$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
		}

		$orderItems = ORM::factory('Order_Item')
						->where("order_id", "=", $order_id)
						->find_all();

		if (!$order->loaded() OR $order->sum <= 0 OR count($orderItems) == 0) {
			//HTTP::redirect("/cart/order/".$order_id."?error=400");
			$errors["null_order"] = "Заказ пуст";
			$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
			return;
		}

		$key = Cart::get_key();

		foreach ($orderItems as $orderItem) {
			$params = json_decode($orderItem->params);

			if ($params->service->name == "kupon") {
					$own_kupons = 0;

					$kupons = ORM::factory('Kupon')
								->where("id","IN",$params->service->ids)
								->find_all();
					
					foreach ($kupons as $kupon) {
						$kupon_is_mine = $kupon->check_and_restore_reserve_if_possible($key);
						$own_kupons = $own_kupons +1;
					}

					$available = FALSE;
					$avail_count = (int) ORM::factory('Kupon_Group', $params->service->group_id)->get_balance() + $own_kupons;
					
					$quantity = Arr::get((array) $params,"quantity".$params->service->group_id, 1);
					if ($avail_count >= $quantity) {
						$available = TRUE;
					}

					if ($available == FALSE) {
						$errors["paid_or_refused"] = $available;
						$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
						return;
					}

					// search kupons dates
					// validate dates
					$reservedCoupons = ORM::factory('Object_Movement')
						->where('order_id', '=', $order_id)
						->where('begin_state', '=', 'avail')
						->where('end_state', '=', 'reserve')
						->find_all();
					$badCoupons = array();
					$timeToCheckSec = 30 * 60;
					foreach($kupons as $coupon) {
						//find in resered
						$reservedCoupon = NULL;
						foreach($reservedCoupons as $reservedCouponItem) {
							if ($coupon->id == $reservedCouponItem->kupon_id) {
								$reservedCoupon = $reservedCouponItem;
								break;
							}
						}
						if ($reservedCoupon == NULL) {
							//not found!
							continue;
						}

						$time = strtotime($reservedCoupon->date);
						if (time() - $time > $timeToCheckSec) {
							$badCoupons []= $coupon;
						}
					}
					if (count($badCoupons) > 0) {
						//prepare data
						$data = array();
						$orderItems = ORM::factory('Order_Item')
							->where("order_id","=", $order->id)
							->find_all();
						foreach($orderItems as $orderItem) {
							$orderItemParams = json_decode($orderItem->params);
							if ($orderItemParams->service->name != 'kupon') {
								continue;
							}
							foreach($badCoupons as $badCoupon) {
								if (in_array($badCoupon->id, $orderItemParams->service->ids)) {
									$data []= $orderItemParams;
								}
							}
						}
						//notify user with bad coupons
						$errors['coupon_reserve_timeout'] = $data;
						$this->return_with_errors('/cart/order/' . $order_id, $this->request->post(), $errors);
						return;
					}
			}
			// if ($params->type == "object") {
			// 	$service = Service::factory("Object", $orderItem->object_id);
			// 	$available = $service->check_available(0);
			// 	if ($available !== TRUE) {
			// 		$errors["paid_or_refused"] = $available;
			// 		$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
			// 		return;
			// 	}
			// }
		}

		if ($order->state > 0) {
			HTTP::redirect($order->payment_url);
			return;
		}

		$robo = new Robokassa($order_id);
		$robo->set_description("Заказ №" . $order_id . ". Ярмарка Онлайн");
		$robo->set_sum($order->sum);
		if ($user AND $user->email)
		{
			$robo->set_email($user->email);
		}
		$payment_url = $robo->get_payment_url();

		$order->key = ($order->user_id) ? NULL : $order->key;
		Cart::clear($order->key, ($user) );
		$order->state = 1;
		$order->payment_url = $payment_url;
		$order->save();
		HTTP::redirect($payment_url);
	}

	public function action_result()
	{
		$sum = $this->request->query("OutSum");
		$order_id = $this->request->query("InvId");
		$signature = $this->request->query("SignatureValue");

		$order = ORM::factory('Order', $order_id);
		$robo = new Robokassa($order_id);
		$robo->set_sum($sum);
		$sample = strtoupper($robo->create_result_sign());

		ORM::factory('Order_Log')->write($order_id, "notice", vsprintf("Сравнение подписи. Подпись ПС: %s, Подпись лок: %s; Сумма ПС: %s, Сумма лок: %s", array($signature, $sample, $sum, $order->sum) ) );

		if ($signature !== $sample OR !$order->loaded() OR (int) $sum <> (int) $order->sum)
		{
			ORM::factory('Order_Log')->write($order_id, "error", vsprintf("!! Не верно сформирована подпись уведомления о платеже (возможно ктото пытается взломать систему). Заказ №%s.", array($order_id) ) );
			echo "bad sign";
			header("HTTP/1.0 404 Not Found");
			exit;
			// echo 1;
		}

		$result = $order->check_state($order->id);
		echo $result.$order->id;
		ORM::factory('Order_Log')->write($order_id, "notice", vsprintf("Конец обработки. Сформирован ответ для платежной системы: %s", array($result.$order->id) ) );
		return;

		// if ($order->state == 1)
		// {
		// 	$order->check_state($order->id);
		// 	echo "OK".$order->id;
		// }
		// else
		// {
		// 	echo 'invoice already paid or refused';
		// }
	}

	public function action_success()
	{

		$order_id = $this->request->query("InvId");
		$sum = $this->request->query("OutSum");
		$signature = $this->request->query("SignatureValue");
		

		$order = ORM::factory('Order', $order_id);

		HTTP::redirect("/cart/order/".$order_id);

		// if ($order->state == 1)
		// {
		// 	$order->check_state($order->id);
		// 	//HTTP::redirect("/cart/order/".$order_id);
		// }
		// else
		// {
		// 	//HTTP::redirect("/cart/order/".$order_id);
		// }
	}

	public function action_to_admin_success()
	{
		if (!Acl::check("pay_service")) {
			throw new HTTP_Exception_404;
			return;
		}

		$order_id = $this->request->post("id");
		$code = $this->request->post("code");

		$order = ORM::factory('Order', $order_id);

		if (!$order->loaded())
		{
			throw new HTTP_Exception_404;
			return;
		}

		$order->fake_command($code, 222);

		HTTP::redirect("/cart/order/".$order->id);
	}

	public function action_fail()
	{

		$order_id = $this->request->query("InvId");
		$sum = $this->request->query("OutSum");
		$signature = $this->request->query("SignatureValue");

		HTTP::redirect("/cart/order/".$order_id);
		
		// $order = ORM::factory('Order', $order_id);
		// if ($order->loaded()) {
		// 	$order->check_state($order->id);
		// 	//HTTP::redirect("/cart/order/".$order->id);
		// 	return;
		// }

		// HTTP::redirect("/");
	}

	private function return_with_errors($uri, $post, $errors) {
		$session = Session::instance();
		$session->set("errors", $errors);
		$session->set("post", $post);
		HTTP::redirect($uri);
	}

	public function json_response()
	{
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}

}