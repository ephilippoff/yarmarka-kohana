<?php

class Model_Order_ItemTemp extends ORM
{
	protected $_table_name = 'order_item_temp';

	function get_info() {
		if (!$this->loaded()) return false;

		if ($this->object_id) {
			return $this->get_object_info();
		} elseif ($this->service_id){
			return true;
		}

		return false;
	}

	function get_object_info()  {

		$result = array();
		$balance = -1;
		$object = ORM::factory('Object', $this->object_id);

		if ($object->loaded()) {
			$result["object_id"] = $object->id;
			$result["title"] = $object->title;
			$result["balance"] = ORM::factory('Object')->get_balance($this->object_id);
			$result["quantity"] = 1;
			$result["price"] = intval($object->price * $result["quantity"]);
		}
		return $result;
	}
}