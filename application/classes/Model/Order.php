<?php

class Model_Order extends ORM
{
	protected $_table_name = 'orders';

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
					$order->success();
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
				}
			}
		}
	}

	function fail() {
		if (!$this->loaded()) return;

		//return goods to storage
		$orderItems = ORM::factory('Order_Item')->get_items($this->id);
		foreach ($orderItems as $orderItem)
		{
			if ($orderItem->object_id) {
				$object = ORM::factory('Object', $orderItem->object_id);
				if ($object->loaded()) {
					$object->increase_balance($object->id, $orderItem->quantity);
				}
			}
		}

		$this->state = 3;
		$this->save();
	}

	function success() {
		if (!$this->loaded()) return;

		$this->state = 2;
		// reload invoice to get payment date
		$this->payment_date = date("Y-m-d H:i:s");
		$this->save();
		
		$user = ORM::factory('User', $this->user_id);

		$orderItems = ORM::factory('Order_Item')->get_items($this->id);

		// send email to user about successfull payment
		if ($user->loaded() AND $user->email)
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
				if ($orderItem->type == "object") {
					Service::factory("Object", $orderItem->object_id)->apply();
					array_push($objects, $orderItem->object_id);
				} else {
					Service::factory(Text::ucfirst($orderItem->type))->apply();
				}
			}

			$db->commit();

		} catch (Kohana_Exception $e) {
			$db->rollback();
			$message =  "Ошибка применения заказа ".$e->getMessage();
			Email::send_to_admin("Ошибка применения заказа #".$order->id, $message);
			return;
		}

	
		$subj = "Уведомление администратору об оплате заказа с товарами. Заказ №".$this->id;
		$msg = View::factory('emails/payment_success',
				array('order' => $this,'orderItems' => $orderItems));

		$configBilling = Kohana::$config->load("billing");
		foreach ($configBilling["emails_for_notify"] as $email) {
			Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}
	
	}
}