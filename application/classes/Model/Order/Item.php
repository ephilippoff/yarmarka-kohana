<?php

class Model_Order_Item extends ORM
{
	protected $_table_name = 'order_item';

	protected $_belongs_to = array(
		'order_obj'			=> array('model' => 'Order', 'foreign_key' => 'order_id'),
	);

	function get_items($order_id) {
		$orderItems = array();
		$_orderItems = $this->where("order_id","=", $order_id)->find_all();
		foreach ($_orderItems as $_orderItem) {
			$params = json_decode($_orderItem->params);
			$description = "";
			if ($params->type == "object") {
					$service = Service::factory("Object", $_orderItem->object_id);
					$available = $service->check_available($params->quantity);
			} elseif ( in_array( $params->type, array("up", "premium") ) ) {
					$service = Service::factory(Text::ucfirst($params->type));

					$available = $service->check_available($params->quantity);
					if ($available) {
						$params->price = 0;
					}
					$description = $service->get_params_description(array(
						"quantity" => $params->quantity,
						"available" => $available
					));
			} else {
				$description = $service->get_params_description(array(
					"quantity" => $params->quantity
				));
				$available = TRUE;
			}
			array_push($orderItems, new Obj(array(
				"id" =>$_orderItem->id,
				"object_id" =>$_orderItem->object_id,
				"service_id" =>$_orderItem->service_id,
				"user_id" =>$_orderItem->order_obj->user_id,
				"title" => $params->title,
				"description" => $description,
				"price" => $params->price,
				"total" => $params->total,
				"available" => $available,
				"type" => $params->type
			)));
		}

		return $orderItems;
	}
}