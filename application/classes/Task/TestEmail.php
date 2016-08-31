<?php defined('SYSPATH') or die('No direct script access.');


class Task_TestEmail extends Minion_Task
{
	protected $_options = array(
		'id' => 0
	);

	protected function _execute(array $params)
	{
		$id 	= $params['id'];
		
		$order = ORM::factory('Order',$id);

		$order_tems = ORM::factory('Order_Item')->where("order_id","=", $order->id)->order_by("id");

		$orderItems = array();
		$sum = Model_Order::each_item($order_tems, function($service, $item, $model_item) use (&$orderItems) {
			$orderItems[] = $item;
			return $item;
		});

		$twig = Twig::factory('emails/payment_success');
		$twig->orderItems = $orderItems;
		$twig->order = $order;

		$msg = $twig->render();

		Minion_CLI::write(
			Email::send('almaznv@yandex.ru', Kohana::$config->load('email.default_from'), 'тестовое пиьсмо об оплате', $msg)
		);

	}

}
