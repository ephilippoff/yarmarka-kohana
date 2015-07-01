<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cart extends Controller_Template {

	protected $json = array();

	public function before()
	{
		parent::before();
	}

	public function action_index()
	{

		$this->assets->js("minified/backbone-min.js");
		$this->assets->js("minified/backbone.marionette.min.js");

		$this->assets->js("cart.js");

		$cart = array();

		$key = $_COOKIE['cartKey'];
		$cartTempItems = array();

		$sum = 0;

		if ($key) {

			$_cartTempItems = ORM::factory('Order_ItemTemp')->where("key","=", $key)->find_all();
			foreach ($_cartTempItems as $_item) {
				$item = array();				
				$item["id"] = $_item->id;				
				$item = array_merge($item, $_item->get_info());

				$params = json_decode($_item->params);
				if ($params->quantity) {
					$item["quantity"] = $params->quantity;
				}
				
				$sum += $item["price"]* $item["quantity"];
				array_push($cartTempItems, new Obj($item));
			}

			$this->template->order = null;
			$order = ORM::factory('Order')->where("key","=",$key)->find();
			if ($order->loaded()) {
				
				$this->template->order = $order;
			}
		}

		$this->template->cartTempItems = $cartTempItems;
		$this->template->sum = $sum;
		$this->template->user = $user = Auth::instance()->get_user();


	}

	public function action_order()
	{
		$id = $this->request->param('id');
		$user = Auth::instance()->get_user();

		if (!$user OR !$id) {
			HTTP::redirect("/cart");
			return;
		}

		$order = ORM::factory('Order')
					->where("user_id","=", $user->id)
					->where("id","=", intval($id))
					->find();
		if (!$order->loaded()) {
			HTTP::redirect("/cart");
			return;
		}

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

		$this->template->state = $state;
		$this->template->order = $order;
		$this->template->orderItems = $orderItems;

		$this->template->getBalance = function($object_id) {
			return ORM::factory('Object')->get_balance($object_id);
		};
		
	}

	public function action_remove_item()
	{
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$key = $_COOKIE['cartKey'];

		if ($key) {
			ORM::factory('Order_ItemTemp')
				->where("key","=", $key)
				->where("id","=", $id)
				->delete_all();
		}

		$this->json["code"] = 200;
		$this->json_response();
	}

	public function action_save()
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
		} catch (Exception $e) {
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}

		$key = $_COOKIE['cartKey'];
		if (!$key) {
			$this->json["message"] = "Неверный ключ";
			$this->json["code"] = 400;
			$this->json_response();
			return;
		}

		$sum = intval($request->sum);

		if ($sum <= 0) {
			$this->json["message"] = "Нулевая сумма заказа";
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
		
		$quantityItems = $request->items;
		$comment = $request->comment;
		$order_id = -1;

		$db = Database::instance();

		try {

			$db->begin();


			$order = ORM::factory('Order')->where("key","=",$key)->find();
			$order_loaded = $order->loaded();
			$order->key = $key;
			$order->user_id = $user->id;
			$order->state = 0;
			$order->sum = $sum;
			$order->comment = $comment;
			$order->params = json_encode(array());
			$order->save();

			$order_id = $order->id;

			$tempItems = ORM::factory('Order_ItemTemp')
									->where("key", "=", $key )
									->find_all();

			if ($order_loaded) {
				ORM::factory('Order_Item')
						->where("order_id","=",$order_id)
						->delete_all();
			}
				
			foreach ($tempItems as $tempItem) {
				$tempItem->params = json_encode(array(
					"quantity" => $quantityItems->{$tempItem->id}
				));

				if ($tempItem->object_id) {
					$balance = ORM::factory('Object')->get_balance($tempItem->object_id);
					$params = new Obj(json_decode($tempItem->params));
					if ($balance >= 0 AND $balance - intval($params->quantity) < 0) {
						throw new Exception($params->title. " недоступен для заказа (остутсвует)");
					}
				}
				
				$tempItem->save();

				$title = $price = null;
				$realItem = ORM::factory('Order_Item');
				$realItem->order_id = $order_id;
				if ($tempItem->object_id) {
					$object = ORM::factory('Object', $tempItem->object_id);
					if (!$object->loaded()) {
						continue;
					}
					$title = $object->title;
					$price = $object->price;
					$realItem->object_id = $tempItem->object_id;

				}
				$realItem->params = json_encode(array(
					"title" => $title,
					"price" => $price,
					"quantity" => $quantityItems->{$tempItem->id}
				));
				$realItem->save();
			}

			$db->commit();

		} catch (Exception $e) {
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

	public function action_pay()
	{
		$this->auto_render = FALSE;

		$user = Auth::instance()->get_user();
		$order_id = $this->request->post("id");

		$order = ORM::factory('Order', $order_id);
		if (!$order->loaded() OR $order->sum <= 0) {
			HTTP::redirect("/cart/order/".$order_id."?error=400");
			return;
		}

		if ($order->state > 0) {
			HTTP::redirect($order->payment_url);
			return;
		}

		$orderItems = ORM::factory('Order_Item')
									->where("order_id", "=", $order_id )
									->find_all();
		foreach ($orderItems as $orderItem) {
			if ($orderItem->object_id) {
				$balance = ORM::factory('Object')->get_balance($orderItem->object_id);
				$params = new Obj(json_decode($orderItem->params));
				if ($balance >= 0 AND $balance - intval($params->quantity) < 0) {
					HTTP::redirect("/cart/order/".$order_id."?error=400");
					return;
				}
			}
		}

		//decrease balance of goods
		foreach ($orderItems as $orderItem) {
			$params = new Obj(json_decode($orderItem->params));
			if ($orderItem->object_id) {
				ORM::factory('Object')->decrease_balance($orderItem->object_id, $params->quantity);
			}
		}

		$robo = new Robokassa($order_id);
		$robo->set_description("Заказ №" . $order_id . ". Ярмарка Онлайн");
		$robo->set_sum($order->sum);
		if ($user AND $user->email)
		{
			$robo->set_email($user->email);
		}
		$payment_url = $robo->get_payment_url();

		unset($_COOKIE['cartKey']);
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

		HTTP::redirect("/");
	}

	public function json_response()
	{
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}

}