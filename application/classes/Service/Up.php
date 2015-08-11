<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Up extends Service
{
	protected $_name = "up";
	protected $_title = "Подъем";
	protected $_is_multiple = FALSE;

	public function __construct($param = NULL)
	{
		$this->_initialize();
	}

	public function get()
	{

		return array(
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $this->getPrice()
		);
	}

	public function apply()
	{

	}
}