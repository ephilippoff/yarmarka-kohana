<?php defined('SYSPATH') OR die('No direct script access.');

class Service_NewsPaper extends Service
{
	const NEWSPAPER_SETTING_NAME = 'newspaper';
	public static $_types = array(
		"free" => "Бесплатное",
		"point" => "С точкой",
		"photo" => "Фото объявление",
		"green_border" => "В зеленой рамке",
		"red_border" => "В розовой рамке",
		"black_border" => "В черной рамке",
		"blue_border" => "В синей рамке",
		"yellow_marker" => "Маркер жёлтый",
		"blue_marker" => "Маркер синий",
		"red_marker" => "Маркер красный",
	);

	public static $_cities = array(
		"surgut" => "Ярмарка - Сургут",
		"nizhnevartovsk" => "Ярмарка - Нижневартовск",
		"tyumen" => "Ярмарка - Тюмень"
	);

	protected $_name = "newspaper";
	protected $_title = "Объявление в газету";
	protected $_is_multiple = TRUE;
	public $_contents = array();

	public function __construct($object_id = NULL)
	{
		$object = ORM::factory('Object',$object_id);
		if ($object->loaded()) {
			$this->object($object);
		}
		$this->_initialize();
	}

	public function get_info()
	{
		return array(
			"types" => self::$_types,
			"prices" => $this->_price_config
		);
	}

	public function get()
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getNewsPaperPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description().$discount_reason;
		$city = $this->city();
		$contents = $this->contents();
		$types =  array_keys($contents);
		if ($types == array("free")) {
			$discount_name = "free_advert";
		}
		return array(
			"contents" => $contents,
			"city" => ($this->city()) ? $city : NULL,
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

	public function getNewsPaperPrice()
	{
		$contents = $this->contents();
		$city = $this->city();
		$price_config = $this->_price_config;
		$result = 0;
		foreach ($contents as $key => $value) {
			$result += $price_config[$city][$key] * $value;
		}
		return $result;
	}

	public function get_params_description($params =  array())
	{
		$contents = $this->contents();
		$city = Arr::get(self::$_cities, $this->city(), "");
		$result = array();
		foreach ($contents as $key => $value) {
			$value = (int) $value;
			$result[] = self::$_types[$key]."(".$value.")";
		}
		return $city."; ".implode(", ", $result);
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->types) 
		{
			$contents = array();
			foreach ($params->types as $type) {
				$contents[$type] = $params->{"quantity_".$type};
			}

			$this->contents($contents);
		}

		if ($params->city) 
		{
			$this->city($params->city);
		}

	}

	public function contents($contents = NULL)
	{
		if (!$contents) return $this->_contents;
		$this->_contents = $contents;
		return $this;
	}

	public function city($city = NULL)
	{
		if (!$city) return $this->_city;
		$this->_city = $city;
		return $this;
	}

	public function apply($orderItem)
	{
		$object_id = $orderItem->object->id;
		self::saveServiceInfoToCompiled($object_id);
	}

	static function apply_service($object_id, $quantity, $cities = NULL, $categories = NULL)
	{
		
	}
}