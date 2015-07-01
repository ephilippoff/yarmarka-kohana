<?php

class Model_Order extends ORM
{
	protected $_table_name = 'orders';

	function check_state($order_id = NULL) {

		$robo = new Robokassa;

		if ($order_id) {
			$orders = ORM::factory('Order')->where("id","=", $order_id)->find_all();
		} else {
			$orders = ORM::factory('Order')->where("state","=",1)->find_all();
		}
		
		foreach ($orders as $order) {
			$data = $robo->get_invoice_state($order->id);
			if ($data)
			{
				if ($data['code_request'] == 10 OR $data['code_request'] == 60)
				{
					$order->fail();
				}
				elseif ($data['code_request'] == 100)
				{
					$order->success();
				}
			}
		}
	}

	function fail() {
		if (!$this->loaded()) return;

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

		$objects = array();
		//apply services
		foreach ($orderItems as $orderItem)
		{
			if ($orderItem->object_id) {
				array_push($objects, $orderItem->object_id);
			} 
		}

		if (count($objects) > 0) {
			$subj = "Уведомление администратору об оплате заказа с товарами. Заказ №".$this->id;
			$msg = View::factory('emails/payment_success',
					array('order' => $this,'orderItems' => $orderItems));

			$configBilling = Kohana::$config->load("billing");
			foreach ($configBilling["emails_for_notify"] as $email) {
				Email::send($email, Kohana::$config->load('email.default_from'), $subj, $msg);
			}
		}
	}
}