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

	function save_service_params($new_service_params)
	{
		if (!$this->loaded()) return;
		$params = json_decode($this->params);
		$params->service = $new_service_params;
		$this->params = json_encode($params);
		$this->save();
	}

	function save_object_to_order_item_temp($object_id, $key, $params = array()) {

		$this->db->insert('order_item_temp', array(
			'object_id' => $object_id,
			'key' => $key,
			'params' => json_encode($params)
		));

		return $this->db->insert_id();
	}

	function return_reserve()
	{
		$user = Auth::instance()->get_user();
		if (!$this->loaded()) return;
		

		$params = new Obj(json_decode($this->params));
		if ($params->service->name == "kupon")
		{
			$service = Service::factory(Text::ucfirst($params->service->name), $params->service->group_id);
			$service->return_reserve($params->service->ids);

		} elseif ( in_array( $params->service->name, array("up", "premium")) 
					AND in_array(@$params->service->discount_name, array("free_up", "prepayed_premium") ) AND $user) {
			 $service = Service::factory(Text::ucfirst($params->service->name));
			 $service->increase_balance($user, $params->service->quantity);
		}
	}

	function reserve($access_key = NULL)
	{
		$user = Auth::instance()->get_user();
		if (!$this->loaded()) return;

		$params = new Obj(json_decode($this->params));
		if ($params->service->name == "kupon") {
			$service = Service::factory(Text::ucfirst($params->service->name), $params->service->group_id);
			$service->reserve($params->service->ids, $access_key);

		} elseif ( in_array( $params->service->name, array("up", "premium")) 
					AND in_array(@$params->service->discount_name, array("free_up", "prepayed_premium") ) AND $user) {
			 $service = Service::factory(Text::ucfirst($params->service->name));
			 $service->decrease_balance($user, $params->service->quantity);
		}
	}

}