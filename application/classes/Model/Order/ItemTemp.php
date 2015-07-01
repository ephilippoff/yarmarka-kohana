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

	function get_balance($object_id) {
		$_balance = ORM::factory('Data_Integer')
					->by_object_and_attribute($object_id, "balance");
		if ($_balance->loaded()) {
			$balance = intval($_balance->value_min);
		} else {
			$balance = -1;
		}
		return $balance;
	}

	function get_object_info()  {

		$result = array();
		$balance = -1;
		$object = ORM::factory('Object', $this->object_id);

		if ($object->loaded()) {
			$result["object_id"] = $object->id;
			$result["title"] = $object->title;
			$result["balance"] = $this->get_balance($this->object_id);
			$result["quantity"] = 1;
			$result["price"] = intval($object->price * $result["quantity"]);
		}
		return $result;
	}
}