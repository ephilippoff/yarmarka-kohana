<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(
		"number" => NULL,
		"code" => NULL
	);

	protected function _execute(array $params)
	{
		$items = ORM::factory('Order_Item')->where('service_name','IS',NULL)->find_all();

		foreach ($items as $item) {
			 
			$data = json_decode($item->params);

			Minion_CLI::write( $data->service->name );
			$oi = ORM::factory('Order_Item',$item->id);
			$oi->service_name = $data->service->name;
			$oi->update();
		}

	}

}
