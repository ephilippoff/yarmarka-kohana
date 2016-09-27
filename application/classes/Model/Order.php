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

		$return = "!";

		$params = new Obj($params);
		$robo = new Robokassa;

		if ($order_id) {
			$orders = ORM::factory('Order')->where("id","=", $order_id)->find_all();
		} else {
			$orders = ORM::factory('Order')->where("state","in",array(0,1))->find_all();
			$ids = array();
			foreach ($orders as $order) {
				$ids[] = $order->id;
			}
			ORM::factory('Order_Log')->write(NULL, "notice", vsprintf("Запрашиваем состояние оплаты заказов (выставленных но не оплаченных) у ПС: %s", array( join(",", $ids) ) ) );
		}
		
		foreach ($orders as $order) {

			if ($params->fake)
			{
				$data = $params->fake;
				if ($params->fake_state == 22) {
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Дана команда активации заказа без оплаты (100 проц. скидка).  № %s", array($order->id) ) );
				} else if ($params->fake_state == 222) {
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Дана команда активации заказа без оплаты (АДМИН).  № %s", array($order->id) ) );
				}
			}
			else
			{
				ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Запрашиваем состояние оплаты заказа у ПС.  № %s", array($order->id) ) );
				$data = $robo->get_invoice_state($order->id);
			}
			
			if ($data)
			{
				if ($data['code_request'] == 10 OR $data['code_request'] == 60)
				{
					$return = 'FAIL';
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Оплата заказа отменена. № %s", array($order->id) ) );
					$order->fail();
					if ($callback) {
						$callback($order->id, "cancel");
					}
				}
				elseif ($data['code_request'] == 100)
				{
					if ($params->fake) {
						$return = 'ADMIN';
						if ($params->fake_state == 22) {
							ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Заказ активирован (100 проц. скидка).  № %s", array($order->id) ) );
						} else if ($params->fake_state == 222) {
							ORM::factory('Order_Log')->write($order->id, "warning", vsprintf("Активирован с помощью спец кнопки (АДМИН).  № %s", array($order->id) ) );
						}
						$order->success($params->fake_state);
					} else {
						$return = 'OK';
						ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Заказ оплачен. № %s", array($order->id) ) );
						$order->success();
					}

					if ($callback) {
						$callback($order->id, "success");
					}
				} else {
					$return = 'WAIT';
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Заказ в ожидании оплаты. № %s", array($order->id) ) );
					if ($callback) {
						$callback($order->id, "wait");
					}
				}
			} else {

				ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Проверка истечения времени оплаты заказа ", array($order->id) ) );

				if (strtotime(date('Y-m-d H:i:s')) > strtotime($order->created) + 60*30) {
					$return = 'FAIL';
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Время оплаты заказа истекло. Отменяем ", array($order->id) ) );
					$order->fail();
					if ($callback) {
						$callback($order->id, "cancel");
					}
				} else {
					$return = 'WAIT';
					ORM::factory('Order_Log')->write($order->id, "notice", vsprintf("Заказ в ожидании оплаты ", array($order->id) ) );
					if ($callback) {
						$callback($order->id, "wait");
					}
				}
			}

			
		}

		if ($order_id) {
			return $return;
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
		if ($user->loaded() AND $user->email AND in_array($this->state, array(2)))
		{				
			ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Отправка письма пользователю о успехе оплаты. email: %s,  № %s", array($user->email, $this->id) ) );

			$last_object = ORM::factory('Object')->where('author','=',$user->id)->order_by('id','desc')->find();

			$params = array(
				'order' => $this,
			    'orderItems' => $orderItems,
			    'domain' => ($last_object->loaded()) ? $last_object->city_id : FALSE
			);

			Email_Send::factory('payment_success')
					->to( $user->email )
					->set_params($params)
					->set_utm_campaign('payment_success')
					->send();
		}

		$db = Database::instance();

		try {

			$db->begin();

			$objects = array();
			//apply services
			foreach ($orderItems as $orderItem)
			{
				//if ($orderItem->service->name == "kupon") {
					Service::factory(Text::ucfirst($orderItem->service->name))->apply($orderItem);
				//}
			}

			$db->commit();

		} catch (Kohana_Exception $e) {
			$db->rollback();
			ORM::factory('Order_Log')->write($this->id, "error", vsprintf("Ошибка активации услуг : %s, заказ № %s", array($e->getMessage(), $this->id) ) );
			$message =  "Ошибка применения заказа ".$e->getMessage();
			Email::send_to_admin("Ошибка применения заказа #".$this->id, $message);
			return;
		}

		if ( in_array($this->state, array(2)) )
		{
			$configBilling = Kohana::$config->load("billing");

			$subj = "Уведомление администратору об оплате заказа с товарами. Заказ №".$this->id;
			
			$twig = Twig::factory('emails/payment_success');
			$twig->orderItems = $orderItems;
			$twig->order = $this;

			$msg = $twig->render();

			ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Отправка уведомления администраторам об оплате заказа с товарами. emailы: %s,  № %s", array( join(", ", $configBilling["emails_for_notify"]), $this->id) ) );
			
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

	function electronic_delivery($orderItem, $kupons)
	{
		if (!$this->loaded()) return;

		$params = ($this->params) ? $this->params : "{}";
		$params = new Obj(json_decode($params));

		if ($params->delivery AND $params->delivery->type == "electronic" AND $orderItem->service->name == "kupon") {
				$subj = "Вы приобрели купоны на скидку. Заказ №".$this->id;
				$msg = View::factory('emails/kupon_notify',
						array(
							'title' => $orderItem->service->title,
							'kupons' => $kupons,
							'key' => $orderItem->kupon->access_key,
							'order' => $this,
							'object_id' => $orderItem->object->id,
							'for_supplier' => FALSE
						));

				ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Электронная доставка купона. email: %s,  № %s", array($params->delivery->email, $this->id) ) );
				
				Email::send($params->delivery->email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}
	}

	function sms_delivery($orderItem, $kupons)
	{
		if (!$this->loaded()) return;

		$params = ($this->params) ? $this->params : "{}";
		$params = new Obj(json_decode($params));

		if ($params->delivery AND $params->delivery->type == "electronic" AND $orderItem->service->name == "kupon" AND $params->delivery->phone) {

			ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Смс клиенту, с номером купона. Телефон: %s,  № %s", array($params->delivery->phone, $this->id) ) );

			$phone = Text::clear_phone_number($params->delivery->phone);
			if ($phone AND Valid::is_mobile_contact($phone)) {
				$message = Kohana::$config->load('sms.messages.kupon_notify');
				foreach ($kupons as $kupon) {
					Sms::send($phone, sprintf($message, Text::format_kupon_number(Model_Kupon::decrypt_number($kupon->number))), NULL);
				}
				
			}
		}
		
	}

	function supplier_delivery($orderItem, $kupons, $phone, $email, $support_emails)
	{
		if (!$this->loaded()) return;

		$params = ($this->params) ? $this->params : "{}";
		$params = new Obj(json_decode($params));

		if ($phone AND Valid::is_mobile_contact($phone)) {
			$message = Kohana::$config->load('sms.messages.kupon_supplier_notify');

			ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Смс поставщику, Телефон: %s,  № %s", array($phone, $this->id) ) );

			foreach ($kupons as $kupon) {
				$number = Model_Kupon::decrypt_number($kupon->number);
				$number =substr($number, count($number) - 4, 3);
				Sms::send(
					$phone,
					sprintf(
						$message, 
						$orderItem->service->title,
						( ($params->delivery AND $params->delivery->name) ? $params->delivery->name: "" ),//name,
						( ($params->delivery AND $params->delivery->phone) ? $params->delivery->phone: "" ),//phone,
						( ($number) ? "№ ***-***-".$number : "" )//last_digits
					),
					NULL
				);
			}
		}
		$group = ORM::factory('Kupon_Group', $orderItem->service->group_id);
		$subj = "Приобретены купоны на скидку. Заказ №".$this->id;
		$msg = View::factory('emails/kupon_notify',
				array(
					'title' => $orderItem->service->title,
					'kupons' => $orderItem->service->ids,
					'key' => $orderItem->kupon->access_key,
					'order' => $this,
					'object_id' => $orderItem->object->id,
					'for_supplier' => TRUE,
					'delivery' => $params->delivery,
					'avail_balance' => $group->get_balance(),
					'sold_balance' => $group->get_sold_balance()
				));

		if ($email AND Valid::is_email_contact($email)) {

			ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Email поставщику: %s,  № %s", array($email, $this->id) ) );

			Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}

		try {
			if ($support_emails  AND count($support_emails) > 0) {

				ORM::factory('Order_Log')->write($this->id, "notice", vsprintf("Уведомления сотрудникам: %s,  № %s", array( join(", ", $support_emails) , $this->id) ) );

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
			$key = Cookie::dget("cartKeyS");
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