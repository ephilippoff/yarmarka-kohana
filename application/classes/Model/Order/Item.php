<?php

class Model_Order_Item extends ORM
{
	protected $_table_name = 'order_item';


	function get_items($order_id) {
		$orderItems = array();
		$_orderItems = $this->where("order_id","=", $order_id)->find_all();
		foreach ($_orderItems as $_orderItem) {
			$params = json_decode($_orderItem->params);
			array_push($orderItems, new Obj(array(
				"id" =>$_orderItem->id,
				"object_id" =>$_orderItem->object_id,
				"service_id" =>$_orderItem->service_id,
				"title" => $params->title,
				"quantity" => $params->quantity,
				"price" => $params->price
			)));
		}

		return $orderItems;
	}
}