<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cart extends Controller_Template {

	protected $json = array();

	public function before()
	{
		parent::before();

		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->domain = new Domain();
		if ($proper_domain = $this->domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}
	}

	public function action_index()
	{
		$start = microtime(true);
		$twig = Twig::factory('cart/index');

		$cart = $cartTempItems = $sale_types = array();
		$key = Cart::get_key();

		$twig->crumbs = array(
			array(
				"title" => "Оформление заказа"
			)
		);

		$twig->city = $this->domain->get_city();

		$sum = 0;
		//если корзина не пуста, т.е. есть ключ
		if ($key) {

			$_cartTempItems = ORM::factory('Order_ItemTemp')
								->where("key","=", $key)->order_by("id")->find_all();

			foreach ($_cartTempItems as $_item) {
				$params = json_decode($_item->params);
				$service = Service::factory(Text::ucfirst($params->type), $_item->object_id);

				$item = array();
				$item = array_merge($item, (array) $params);
				$item["id"] = $_item->id;

				//если заказан товар , проверяем доступность, и информацию по доставке
				if ($params->type == "object") {
					
					$sale_type = $service->get_delivery_info();
					$available = $service->check_available($params->quantity);
					$item["available"] = $available;
					$item["balance"] = $service->getBalance();
					if ($sale_type)
					{
						array_push($sale_types, $sale_type);
					}
				} elseif ( in_array( $params->type, array("up", "premium",) ) ) {
					$item["available"] = $service->check_available($params->quantity);
					if ($item["available"]) {
						$item["price"] = 0;
					}
					$item["description"] = $service->get_params_description(array(
						"quantity" => $item["quantity"],
						"available" => $item["available"]
					));
				} else {
					$item["available"] = FALSE;
					$item["description"] = $service->get_params_description(array(
						"quantity" => $item["quantity"],
						"available" => $item["available"]
					));
				}

				$sum += $item["price"] * $item["quantity"];

				array_push($cartTempItems, new Obj($item));
			}

			$twig->order = null;
			//если уже был сохранен заказ
			$order = ORM::factory('Order')->where("key","=",$key)->find();
			if ($order->loaded()) {
				$twig->order = $order;
			}
		}

		

		if (in_array("with-shipping", $sale_types)) {
			$twig->next_page = "cart/delivery/";
		} else {
			$twig->next_page = "cart/order/";
		}
		

		$twig->cartTempItems = $cartTempItems;
		$twig->sum = $sum;
		$twig->user = $user = Auth::instance()->get_user();

		$twig->php_time = microtime(true) - $start;
		$this->response->body($twig);
	}

	public function action_delivery()
	{

		$start = microtime(true);
		$twig = Twig::factory('cart/delivery');
		$twig->city = $this->domain->get_city();
		$id = $this->request->param('id');
		$user = Auth::instance()->get_user();
		$errors = array();

		if (!$user OR !$id) {
			HTTP::redirect("/cart");
			return;
		}

		$order = ORM::factory('Order')
					->where("user_id","=", $user->id)
					->where("id","=", intval($id))
					->find();
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

		$twig->php_time = microtime(true) - $start;
		$this->response->body($twig);
	}


	public function action_order()
	{
		$start = microtime(true);
		$twig = Twig::factory('cart/order');
		$twig->city = $this->domain->get_city();

		$id = $this->request->param('id');
		$user = Auth::instance()->get_user();

		$session = Session::instance();
		$errors = $session->get("errors");
		$errors_post = $session->get("post");
		$session->delete("errors");
		$session->delete("post");

		if (!$user OR !$id) {
			throw new HTTP_Exception_404;
			return;
		}

		$order = ORM::factory('Order')
					->where("user_id","=", $user->id)
					->where("id","=", intval($id))
					->find();
		if (!$order->loaded()) {
			throw new HTTP_Exception_404;
			return;
		}

		$twig->crumbs = array(
			array(
				"title" => "Оформление заказа",
				"url" => "cart"
			)
		);

		$params = ($order->params) ? $order->params : "{}";
		$params = new Obj(json_decode($params));
		if ($params->delivery)
		{

			$twig->crumbs[] = array(
				"title" => "Оформление доставки",
				"url" => "cart/delivery/".$order->id
			);

			$twig->delivery_info = $params->delivery;
		}

		$twig->crumbs[] = array(
			"title" => "Подтверждение заказа"
		);

		$orderItems = ORM::factory('Order_Item')->get_items($order->id);

		$state = "initial";

		if ($order->state == 0) {
			$state = "initial";
		} elseif ($order->state == 1) {
			$state = "notPaid";
		} elseif ($order->state == 2) {
			$state = "paid";
		} elseif ($order->state == 3) {
			$state = "cancelPayment";
		} else {
			$state = "cancelPayment";
		}

		if ($state <> "initial")
		{
			$twig->crumbs = array(
				array(
					"title" => "Заказ №".$order->id
				)
			);
		}

		$twig->state = $state;
		$twig->order = $order;
		$twig->orderItems = $orderItems;

		$twig->errors = $errors;
		$twig->errors_post = new Obj(($errors_post)?$errors_post:array());

		$twig->php_time = microtime(true) - $start;
		$this->response->body($twig);
	}

	public function action_saveorder()
	{
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		$this->auto_render = FALSE;		
		$user = Auth::instance()->get_user();

		if (!$user) {
			$this->json["message"] = "Требуется авторизация";
			$this->json["code"] = 403;
			$this->json_response();
			return;
		}

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

		$db = Database::instance();

		try {

			$db->begin();

			$order = ORM::factory('Order')
						->where("key","=",$key)
						->where("state","=",0)
						->find();
			$order_loaded = $order->loaded();

			$order->key = $key;
			$order->user_id = $user->id;
			$order->state = 0;
			$order->sum = 0;
			$order->params = ($order->params) ? $order->params : "{}";
			$order->save();

			$order_id = $order->id;

			//return balance of goods if edit cart
			if ($order_loaded)
			{
				$order->return_reserve();
				ORM::factory('Order_Item')
						->where("order_id","=",$order_id)
						->delete_all();
			}

			$tempItems = ORM::factory('Order_ItemTemp')
									->where("key", "=", $key)
									->find_all();
			$sum = 0;

			foreach ($tempItems as $tempItem)
			{
				$params = new Obj(json_decode($tempItem->params));
				$service = Service::factory(Text::ucfirst($params->type), $tempItem->object_id);
				$description = "";
				if ($params->type == "object" )
				{
					$params->available = $service->check_available($params->quantity);

					if ($params->available !== TRUE)
					{
						$db->rollback();
						$this->json["message"] = "В заказе присутствуют недоступные позиции, удалите их прежде чем продолжить";
						$this->json["code"] = 400;
						$this->json_response();
						return;
					}

				} elseif ( in_array( $params->type, array("up", "premium") ) ) {

					$params->available = $service->check_available($params->quantity);
					if ($params->available) {
						$params->price = 0;
					}

					$description = $service->get_params_description(array(
						"quantity" => $params->quantity,
						"available" => $params->available
					));

				} else {

					$description = $service->get_params_description(array(
						"quantity" => $params->quantity
					));

					$params->available = FALSE;
				}

				$tempItem->params = json_encode((array) $params);
				$tempItem->save();
				
				$realItem = ORM::factory('Order_Item');
				$realItem->order_id = $order_id;
				$realItem->object_id = $tempItem->object_id;
				$realItem->params = json_encode(array(
					"title" => $params->title,
					"price" => $params->price,
					"quantity" => $params->quantity,
					"description" => $description,
					"total" => $params->quantity * $params->price,
					"available" => $params->available,
					"type" => $params->type
				));
				$realItem->save();

				$realItem->reserve();

				$sum += $params->quantity * $params->price;
			}

			$order->sum = $sum ;
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

		$this->json_response();

	}


	public function action_remove_item()
	{
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$key = Cart::get_key();

		if ($key) {
			ORM::factory('Order_ItemTemp')
				->where("key","=", $key)
				->where("id","=", $id)
				->delete_all();
		}

		$this->json["code"] = 200;
		$this->json_response();
	}

	public function action_to_payment_system()
	{
		$this->auto_render = FALSE;
		$errors = array();

		$user = Auth::instance()->get_user();
		$order_id = $this->request->post("id");

		$order = ORM::factory('Order', $order_id);
		$orderItems = ORM::factory('Order_Item')
						->where("order_id", "=", $order_id)
						->find_all();

		if (!$order->loaded() OR $order->sum <= 0 OR count($orderItems) == 0) {
			//HTTP::redirect("/cart/order/".$order_id."?error=400");
			$errors["null_order"] = "Заказ пуст";
			$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
			return;
		}

		foreach ($orderItems as $orderItem) {
			$params = json_decode($orderItem->params);
			if ($params->type == "object") {
				$service = Service::factory("Object", $orderItem->object_id);
				$available = $service->check_available(0);
				if ($available !== TRUE) {
					$errors["paid_or_refused"] = $available;
					$this->return_with_errors("/cart/order/".$order_id, $this->request->post(), $errors);
					return;
				}
			}
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

		Cookie::delete('cartKey');
		ORM::factory('Order_ItemTemp')->where("key", "=", $order->key)->delete_all();

		$order->key = NULL;
		$order->state = 1;
		$order->payment_url = $payment_url;
		$order->save();
		HTTP::redirect($payment_url);
	}

	public function action_result()
	{
		$this->auto_render = FALSE;

		$sum = $this->request->query("OutSum");
		$order_id = $this->request->query("InvId");
		$signature = $this->request->query("SignatureValue");

		$order = ORM::factory('Order', $order_id);

		$robo = new Robokassa($order_id);
		$robo->set_sum($order->sum);
		$sample = strtoupper($robo->create_result_sign());

		if ($signature !== $sample OR !$order->loaded() OR $sum <> $order->sum)
		{
			header("HTTP/1.0 404 Not Found");
			echo "bad sign";
			exit;
		}

		if ($order->state == 1)
		{
			$order->success();
			echo "OK".$order->id;
		}
		else
		{
			echo 'invoice already paid or refused';
		}	
		//HTTP::redirect("/cart");
	}

	public function action_success()
	{
		$this->auto_render = FALSE;

		$order_id = $this->request->post("InvId");
		

		$order = ORM::factory('Order', $order_id);

		if ($order->state == 1)
		{
			$order->check_state($order->id);
			HTTP::redirect("/cart/order/".$order_id);
		}
		else
		{
			HTTP::redirect("/cart/order/".$order_id);
		}
	}

	public function action_to_admin_success()
	{
		$this->auto_render = FALSE;

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

		$order->check_state($order->id, array(
			"fake" => array("code_request" => $code)
		));

		Cookie::delete('cartKey');
		ORM::factory('Order_ItemTemp')->where("key", "=", $order->key)->delete_all();

		HTTP::redirect("/cart/order/".$order->id);
	}

	public function action_fail()
	{
		$this->auto_render = FALSE;

		$order_id = $this->request->post("InvId");

		$order = ORM::factory('Order', $order_id);
		if ($order->loaded()) {
			$order->check_state($order->id);
			HTTP::redirect("/cart/order/".$order->id);
			return;
		}

		Cookie::delete('cartKey');
		ORM::factory('Order_ItemTemp')->where("key", "=", $order->key)->delete_all();

		HTTP::redirect("/");
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