<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Kupon extends Service
{
	protected $_name = "kupon";
	protected $_kupon_group = NULL;
	protected $_title = NULL;
	protected $_is_multiple = FALSE;

	public function __construct($kupon_group_id)
	{
		$kupon_group = ORM::factory('Kupon_Group', $kupon_group_id);
		$this->_kupon_group = $kupon_group;

		$this->_initialize();
	}

	public function _initialize()
	{
		if ($this->_kupon_group->loaded())
		{
			$this->_title = $this->_kupon_group->title;
		}
	}

	public function get()
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description().$discount_reason;
		$kupon = $this->_kupon_group->get_kupon();
		return array(
			"group_id" => $this->_kupon_group->id,
			"id" => $kupon->id,
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $price,
			"quantity" => $quantity,
			"discount" => $discount,
			"discount_name" => $discount_name,
			"discount_reason" => $discount_reason,
			"price_total" => $price_total,
			"description" => $description
		);
	}

	public function get_title($params)
	{
		return $params->service["title"];
	}

	public function get_params_description($params =  array())
	{
		return (($this->quantity()) ? $this->quantity() : 1)." купон";
	}

	public function check_available($quantity)
	{
		$result = TRUE;

		$kupon_group = $this->_kupon_group;

		if ( $kupon_group->get_balance() - $quantity < 0 ) {
			$result = "Недоступен для заказа (отсутсвует)";
			return $result;
		}

		return $result;
	}

	public function get_delivery_info()
	{
		// if ($this->_object->loaded())
		// {
		// 	return $this->_object->get_sale_type($this->_object->id);
		// } else {
			return NULL;
		// }
	}

	public function getPrice($price_base = 1)
	{
		if ($this->_kupon_group->loaded())
		{
			return (float) $this->_kupon_group->price;
		} else {
			return -1;
		}
	}

	public function get_balance()
	{
		if ($this->_kupon_group->loaded())
		{
			return $this->_kupon_group->get_balance();
		} else {
			return 0;
		}
	}

	public function apply($orderItem)
	{
		$oi = ORM::factory('Order_Item', $orderItem->id);

		$kupon = ORM::factory('Kupon', $orderItem->service->id);
		$kupon->to_sold($oi->loaded() ? $oi->order_id : NULL);

		$orderItem->kupon = $kupon;
		$order = ORM::factory('Order', $orderItem->order_id);
		$order->electronic_delivery($orderItem);
	}

	public function return_reserve($id, $description = NULL)
	{
		$kupon = ORM::factory('Kupon', $id);
		$kupon->return_to_avail($description);
	}

	public function reserve($id, $access_key = NULL)
	{
		$kupon = ORM::factory('Kupon', $id);
		$kupon->reserve(NULL, $access_key);
	}

}