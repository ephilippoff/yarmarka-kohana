<?php defined('SYSPATH') or die('No direct script access.');


class Task_Orders extends Minion_Task
{

	protected $_options = array(
		
	);

	protected function _execute(array $params)
	{
		Minion::write("start","orders");
		ORM::factory('Order')->check_state(NULL, function($order_id, $action){
			Minion::write($action, $order_id);
		});
	}

}