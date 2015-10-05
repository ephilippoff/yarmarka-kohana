<?php

class Model_Order_Item extends ORM
{
	protected $_table_name = 'order_item';

	protected $_belongs_to = array(
		'order_obj'			=> array('model' => 'Order', 'foreign_key' => 'order_id'),
	);

	function return_reserve()
	{
		if (!$this->loaded()) return;

		$params = new Obj(json_decode($this->params));
		if ($params->service->name == "kupon")
		{
			$service = Service::factory(Text::ucfirst($params->service->name), $params->service->group_id);
			$service->return_reserve($params->service->ids);
		} elseif ( in_array( $params->service->name, array("up", "premium") ) 
					AND in_array(@$params->service->discount_name, array("free_up", "prepayed_premium") )) {
			$service = Service::factory(Text::ucfirst($params->service->name));
			$user = ORM::factory('User', $this->order_obj->user_id);
			$service->increase_balance($user, $params->service->quantity);
		}
	}

	function reserve()
	{
		if (!$this->loaded()) return;

		$params = new Obj(json_decode($this->params));
		
		if ($params->service->name == "kupon") {
			$service = Service::factory(Text::ucfirst($params->service->name), $params->service->group_id);
			$service->reserve($params->service->ids, $this->order_id);
		} elseif ( in_array( $params->service->name, array("up", "premium") ) 
					AND in_array(@$params->service->discount_name, array("free_up", "prepayed_premium") )) {
			$service = Service::factory(Text::ucfirst($params->service->name));
			$user = ORM::factory('User', $this->order_obj->user_id);
			$service->decrease_balance($user, $params->service->quantity);
		}
	}
}