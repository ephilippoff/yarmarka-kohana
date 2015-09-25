<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Lider extends Service
{
	const LIDER_SETTING_NAME = 'premium';
	const LIDER_DAYS = 7;

	protected $_name = "lider";
	protected $_title = "Лидер";
	protected $_is_multiple = TRUE;
	public $_period = 7;

	public function __construct($object_id = NULL)
	{
		$object = ORM::factory('Object',$object_id);
		if ($object->loaded()) {
			$this->object($object);
		}
		$this->_initialize();
	}

	public function period($period = NULL)
	{
		if (!$period) return $this->_period;
		$this->_period = $period;
		return $this;
	}

	public function get($params = array())
	{
		$params = new Obj($params);
		$quantity = $params->quantity = ($params->quantity) ? $params->quantity : 1;
		$price = $price_total = $this->getPriceMultiple();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description($params).$discount_reason;

		return array(
			"period" => $this->period(),
			"city" => $this->city(),
			"category" => $this->category(),
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

	public function get_params_description($params)
	{
		return "Количество: ".$params->quantity;
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->period) {
			$this->period($params->period);
		}
	}

	public function apply($orderItem)
	{
		$quantity = $orderItem->service->quantity;
		$object_id = $orderItem->object->id;
		$cities = $orderItem->service->city;
		$categories = $orderItem->service->category;

		Service_Lider::apply_service($object_id, $quantity, $cities, $categories);
		self::saveServiceInfoToCompiled($object_id);
	}

	static function apply_service($object_id, $quantity, $cities = NULL, $categories = NULL)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;

		if (!$cities) 
			$cities = array($object->city_id);
		else
			$cities = array_map(function($item) {
				return $item->id;
			} , (array) ORM::factory('City')->where("seo_name","IN", $cities)->getprepared_all());

		if (!$categories) 
			$categories = array($object->category);
		else
			$categories = array_map(function($item) {
				return $item->id;
			} , (array) ORM::factory('Category')->where("seo_name","IN", $categories)->getprepared_all());

		$or = ORM::factory('Object_Service_Photocard')
					->where("object_id", "=", $object_id)
					->where("active","=",1)
					->find_all();
		$find_exact_service = FALSE;
		foreach ($or as $or_item) {
			$_cities = array_map('intval', explode(",", trim(trim($or_item->cities,"{"),"}")));
			$_categories = array_map('intval', explode(",", trim(trim($or_item->categories,"{"),"}")));
			if ( count(array_diff($_cities, $cities)) > 0  OR count(array_diff($_categories, $categories)) > 0) {
				$find_exact_service = TRUE;
				continue;
			} else {
				$find_exact_service = FALSE;
				break;
			}
		}
		if ($find_exact_service OR !isset($or_item) ) {
			$or_item = ORM::factory('Object_Service_Photocard');
		}
		$or_item->object_id = $object_id;
		$or_item->cities = '{'.join(',', $cities).'}';
		$or_item->categories = '{'.join(',', $categories).'}';
		$or_item->active = 1;

		if ($or_item->loaded())
		{
			$or_item->activated = $or_item->activated + $quantity;
		} else {
			$or_item->count = $quantity;
		}

		$or_item->date_expiration = DB::expr("(NOW() + INTERVAL '".Service_Lider::LIDER_DAYS." days')");
		$or_item->save();

		return TRUE;
	}
}