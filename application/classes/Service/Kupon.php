<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Kupon extends Service
{
	protected $_name = "kupon";
	protected $_kupon_group = NULL;
	protected $_title = NULL;
	protected $_is_multiple = FALSE;
	protected $_orderId = NULL;

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
		$kupons = $this->_kupon_group->get_kupon($quantity);
		return array(
			"group_id" => $this->_kupon_group->id,
			"ids" => $kupons,
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

	public function set_params($params = array())
	{
	    $params = new Obj($params);

	    if ($params->quantity) {
	        $this->quantity($params->quantity);
	    }
	}

	public function get_title($params)
	{
		return $params->service["title"];
	}

	public function get_params_description($params =  array())
	{
		return "Количество : ".(($this->quantity()) ? $this->quantity() : 1);
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

		$order = ORM::factory('Order', $orderItem->order_id);

		$kupons = ORM::factory('Kupon')
					->where("id","IN",$orderItem->service->ids)
					->find_all();

		$key = Cart::get_key();
		$kuponsArray = array();
		foreach ($kupons as $kupon) {

			$kupon->to_sold($oi->loaded() ? $oi->order_id : NULL, $key);
			$orderItem->kupon = $kupon;
			$kuponsArray[] = $kupon->get_row_as_obj();

			ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация купона: %s, № %s", array($kupon->id, $orderItem->order_id) ) );
			
		}

		$order->electronic_delivery($orderItem, $kuponsArray);
		$order->sms_delivery($orderItem, $kuponsArray);

		$contactForNotify = ORM::factory('Data_Text')
			->select("seo_name")
			->join("attribute")
				->on("attribute.id","=","data_text.attribute")
			->where("object","=",$orderItem->object->id)
			->where("attribute.seo_name","IN",array("phone","email","support_emails"))
			->getprepared_all();

		$phone = $email = $support_emails = NULL;
		if (count($contactForNotify) > 0 ) {

			$phone = array_filter($contactForNotify, function($item){ return $item->seo_name == "phone"; });
			$email = array_filter($contactForNotify, function($item){ return $item->seo_name == "email"; });
			$support_emails = array_filter($contactForNotify, function($item){ return $item->seo_name == "support_emails"; });
			$phone = (count($phone) > 0) ? (array) array_shift($phone) : NULL;
			$email = (count($email) > 0) ? (array)  array_shift($email) : NULL;
			$support_emails = (count($support_emails) > 0) ? (array) array_shift($support_emails) : NULL;
		}

		$order->supplier_delivery($orderItem, $kupons, $phone['value'], $email['value'], ($support_emails) ? explode(",", $support_emails['value']) : NULL);

	}

	public function return_reserve($ids, $description = NULL, $orderId = NULL)
	{

		ORM::factory('Order_Log')->write(NULL, "notice", vsprintf("Возврат купонов из резерва: %s", array(join(", ",$ids)) ) );

		foreach ($ids as $id) {
			$kupon = ORM::factory('Kupon', $id);
			$kupon->return_to_avail($description, $orderId);
		}
		
	}

	public function reserve($ids, $access_key = NULL, $orderId = NULL)
	{

		ORM::factory('Order_Log')->write(NULL, "notice", vsprintf("Резерв купонов: %s", array(join(", ",$ids)) ) );

		foreach ($ids as $id) {
			$kupon = ORM::factory('Kupon', $id);
			$kupon->reserve($orderId, $access_key);
		}
	}

}