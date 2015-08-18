<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Object extends Service
{
	protected $_name = "object";
	protected $_object = NULL;
	protected $_title = NULL;
	protected $_is_multiple = FALSE;

	public function __construct($object_id)
	{
		$object = ORM::factory('Object',$object_id);
		$this->_object = $object;
		$this->_initialize();
	}

	public function _initialize()
	{
		if ($this->_object->loaded())
		{
			$this->_title = $this->_object->title;
		}
	}

	public function get()
	{

		return array(
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $this->getPrice()
		);
	}

	public function check_available($quantity)
	{
		$result = TRUE;
		$object = $this->_object;
		if (!$object->loaded() or ($object->loaded() and ($object->active == 0 or $object->is_published == 0) ) ) {
			$result = "Снято с продажи";
			return $result;
		}

		$balance = $this->getBalance();
		if ($balance >= 0 AND $balance - intval($quantity) < 0) {
			$result = "Недоступен для заказа (отсутсвует)";
			return $result;
		}

		return $result;
	}

	public function get_delivery_info()
	{
		if ($this->_object->loaded())
		{
			return $this->_object->get_sale_type($this->_object->id);
		} else {
			return NULL;
		}
	}

	public function getPrice()
	{
		if ($this->_object->loaded())
		{
			return $this->_object->price;
		} else {
			return -1;
		}
	}

	public function getBalance()
	{
		if ($this->_object->loaded())
		{
			return $this->_object->get_balance($this->_object->id);
		} else {
			return 0;
		}
	}

	public function apply($orderItem)
	{

	}
}