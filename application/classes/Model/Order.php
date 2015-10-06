<?php

class Model_Order extends ORM
{
	protected $_table_name = 'orders';

	protected $_belongs_to = array(
		'user_obj'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

	function fake_command($code, $state = 2)
	{
		$user = Auth::instance()->get_user();
		
		$this->check_state($this->id, array(
			"fake" => array("code_request" => $code),
			"fake_state" => $state
		));

		Cart::clear($this->key, ($user) );

		$this->key = ($this->user_id) ? NULL : $this->key;
		$this->save();
	}

	function check_state($order_id = NULL, $params = array(), $callback = NULL) {

		$params = new Obj($params);
		$robo = new Robokassa;

		if ($order_id) {
			$orders = ORM::factory('Order')->where("id","=", $order_id)->find_all();
		} else {
			$orders = ORM::factory('Order')->where("state","in",array(0,1))->find_all();
		}
		
		foreach ($orders as $order) {

			if ($params->fake)
			{
				$data = $params->fake;
			}
			else
			{
				$data = $robo->get_invoice_state($order->id);
			}
			
			if ($data)
			{
				if ($data['code_request'] == 10 OR $data['code_request'] == 60)
				{
					$order->fail();
					if ($callback) {
						$callback($order->id, "cancel");
					}
				}
				elseif ($data['code_request'] == 100)
				{
					$order->success($params->fake_state);
					if ($callback) {
						$callback($order->id, "success");
					}
				} else {
					if ($callback) {
						$callback($order->id, "wait");
					}
				}
			} else {

				if (strtotime(date('Y-m-d H:i:s')) > strtotime($order->created) + 60*30) {
					$order->fail();
					if ($callback) {
						$callback($order->id, "cancel");
					}
				} else {
					if ($callback) {
						$callback($order->id, "wait");
					}
				}
			}
		}
	}

	function fail() {
		if (!$this->loaded()) return;

		//return goods to storage
		$this->return_reserve();

		$this->state = 3;
		$this->save();
	}

	function success($state = NULL) {
		if (!$this->loaded()) return;

		$this->state = ($state) ? $state : 2;
		// reload invoice to get payment date
		$this->payment_date = date("Y-m-d H:i:s");
		$this->save();
		
		$user = ORM::factory('User', $this->user_id);

		$order_tems = ORM::factory('Order_Item')->where("order_id","=", $this->id)->order_by("id");

		$orderItems = array();
		$sum = Model_Order::each_item($order_tems, function($service, $item, $model_item) use (&$orderItems) {
			$orderItems[] = $item;
			return $item;
		});

		// send email to user about successfull payment
		if ($user->loaded() AND $user->email AND $state == 2)
		{				
			
			$subj = "Потверждение оплаты. Заказ №".$this->id;
			$msg = View::factory('emails/payment_success',
					array('order' => $this,'orderItems' => $orderItems));

			Email::send($user->email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}

		$db = Database::instance();

		try {

			$db->begin();

			$objects = array();
			//apply services
			foreach ($orderItems as $orderItem)
			{
				if ($orderItem->service->name == "kupon") {
					Service::factory(Text::ucfirst($orderItem->service->name))->apply($orderItem);
				}
			}

			$db->commit();

		} catch (Kohana_Exception $e) {
			$db->rollback();
			$message =  "Ошибка применения заказа ".$e->getMessage();
			Email::send_to_admin("Ошибка применения заказа #".$this->id, $message);
			return;
		}

		if ($state == 2)
		{
			$configBilling = Kohana::$config->load("billing");

			$subj = "Уведомление администратору об оплате заказа с товарами. Заказ №".$this->id;
			$msg = View::factory('emails/payment_success',
					array('order' => $this,'orderItems' => $orderItems));

			
			foreach ($configBilling["emails_for_notify"] as $email) {
				Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
			}
		}
	
	}

	function return_reserve()
	{
		if (!$this->loaded()) return;

		$orderItems = ORM::factory('Order_Item')
						->where("order_id", "=", $this->id )
						->find_all();
		foreach ($orderItems as $orderItem) $orderItem->return_reserve();
	}

	static function each_item($query, $callback)
	{
		$sum = 0;
		$cart_items = $query->find_all();

		foreach ($cart_items as $cart_item) {
			$params = json_decode($cart_item->params);
			$service = Service::factory(Text::ucfirst($params->service->name), ($params->service->name == "kupon") ? $params->service->group_id: $cart_item->object_id);

			$cart_item_obj = $cart_item->get_row_as_obj();
			$item = new Obj((array) $params);
			$item->id = $cart_item->id;
			$item->available = FALSE;
			$item->order_id = @$cart_item_obj->order_id;

			$item = $callback($service, new Obj($item), $cart_item);

			$sum += $item->service->price_total;
		}

		return $sum;
	}

	static function get_state($stateId, $get_name = FALSE)
	{
		if ($stateId == 0) {
			$state = "Инициирован";
			$state_name = "initial";
		} elseif ($stateId == 1) {
			$state = "В ожидании оплаты";
			$state_name = "notPaid";
		} elseif ($stateId == 2) {
			$state = "Оплачен";
			$state_name = "paid";
		} elseif ($stateId == 22) {
			$state = "Активирован без оплаты";
			$state_name = "activate";
		} elseif ($stateId == 222) {
			$state = "Активирован без оплаты Администратором";
			$state_name = "adminActivate";
		} elseif ($stateId == 3) {
			$state = "Отменен";
			$state_name = "cancelPayment";
		} else {
			$state = "Отменен";
			$state_name = "cancelPayment";
		}
		return ($get_name) ? $state_name : $state;
	}

	function electronic_delivery($orderItem)
	{
		if (!$this->loaded()) return;

		$params = ($this->params) ? $this->params : "{}";
		$params = new Obj(json_decode($params));

		if ($params->delivery AND $params->delivery->type == "electronic" AND $orderItem->service->name == "kupon") {
				$subj = "Вы приобрели купоны на скидку. Заказ №".$this->id;
				$msg = View::factory('emails/kupon_notify',
						array(
							'title' => $orderItem->service->title,
							'ids' => $orderItem->service->ids,
							'key' => $orderItem->kupon->access_key,
							'order' => $this,
							'for_supplier' => FALSE
						));
				
				Email::send($params->delivery->email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}
	}

	function supplier_delivery($orderItem, $phone, $email, $support_emails)
	{
		if (!$this->loaded()) return;

		$params = ($this->params) ? $this->params : "{}";
		$params = new Obj(json_decode($params));

		if ($phone AND Valid::is_mobile_contact($phone)) {
			Sms::send($phone, 'Приобретен купон: '.$orderItem->service->title, NULL);
		}
		$group = ORM::factory('Kupon_Group', $orderItem->service->group_id);
		$subj = "Приобретены купоны на скидку. Заказ №".$this->id;
		$msg = View::factory('emails/kupon_notify',
				array(
					'title' => $orderItem->service->title,
					'ids' => $orderItem->service->ids,
					'key' => $orderItem->kupon->access_key,
					'order' => $this,
					'for_supplier' => TRUE,
					'delivery' => $params->delivery,
					'avail_balance' => $group->get_balance(),
					'sold_balance' => $group->get_sold_balance()
				));

		if ($email AND Valid::is_email_contact($email)) {
			Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}

		try {
			if ($support_emails  AND count($support_emails) > 0) {
				foreach ($support_emails as $email) {
					Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
					
				}
			}
		} catch (Exception $e) {
			
		}
	}
	

	static function GetMyLastOrder()
	{	
		$user = Auth::instance()->get_user();
		if ($user) {
			$last_order = ORM::factory('Order')
					->where("user_id","=",$user->id)
					->where("state","IN",array(0,1))
					->order_by("id","desc")
					->find();
		} else {
			$key = Cookie::dget("cartKey");
			if (!$key) return;
			$last_order = ORM::factory('Order')
					->where("key","=",$key)
					->where("state","IN",array(0,1))
					->order_by("id","desc")
					->find();
		}
		

		
		if (!$last_order->loaded()) return;
		return $last_order->get_row_as_obj();
	}

}