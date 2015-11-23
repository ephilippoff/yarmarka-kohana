<?php 
	defined('SYSPATH') or die('No direct script access.');

	class Controller_Test extends Controller_Template {

		function action_index() {
			$order_id = $this->request->query('order_id');
			$order = ORM::factory('Order', $order_id);
			$order_tems = ORM::factory('Order_Item')->where("order_id","=", $order_id)->order_by("id");

			$orderItems = array();
			$sum = Model_Order::each_item($order_tems, function($service, $item, $model_item) use (&$orderItems) {
				$orderItems[] = $item;
				return $item;
			}); 

			$subj = "Уведомление администратору об оплате заказа с товарами. Заказ №".$order->id;
			$msg = View::factory('emails/payment_success', array('order' => $order,'orderItems' => $orderItems));

			Email::send($this->request->query('to'), Kohana::$config->load('email.default_from'), $subj, $msg);
			die;
		}

	}