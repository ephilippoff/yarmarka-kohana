<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Lider extends Service
{
	const LIDER_SETTING_NAME = 'lider';
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

	public function get()
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getPriceMultiple();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$available = $this->check_available($quantity);
		if ($available) {
			$discount = $price * $quantity;
			$discount_reason = " (предоплаченный)";
			$discount_name = "prepayed_lider";
		}
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description().$discount_reason;

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

	public function get_params_description($params = array())
	{
		return "Количество: ".(($this->quantity()) ? $this->quantity() : 1);
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->period) {
			$this->period($params->period);
		}

		return $this;
	}

	public function apply($orderItem)
	{
		$quantity = $orderItem->service->quantity;
		$object_id = $orderItem->object->id;
		$cities = $orderItem->service->city;
		$categories = $orderItem->service->category;

		Service_Lider::apply_service($object_id, $quantity, $cities, $categories);
		self::saveServiceInfoToCompiled($object_id);

		ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги Лидер: № %s", array( $orderItem->order_id ) ) );

	}

	static function apply_service($object_id, $quantity, $cities = NULL, $categories = NULL, $auto_activated = FALSE)
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

		$object->prolong();
		
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
			$or_item->activated = $or_item->activated + 1;

			if (!$auto_activated) {
				$or_item->count = $or_item->count + $quantity;
			}

		} else {
			$or_item->count = $quantity;
		}

		$or_item->date_expiration = DB::expr("(NOW() + INTERVAL '".Service_Lider::LIDER_DAYS." days')");


		$or_item->save();

		return TRUE;
	}

	public function check_available($quantity, $balance = FALSE)
	{
		$result = FALSE;
		$quantity = ($quantity) ? $quantity : 1;
		if ($quantity > 1) return $result;

		$user = Auth::instance()->get_user();
		if (!$user) return $result;

		$balance = ($balance) ? $balance : self::get_balance($user);

		if ($balance >= 0 AND $balance - intval($quantity) >= 0) {
			return TRUE;
		}

		return $result;
	}

	static function get_balance($user = NULL)
	{
		if (!$user) 
			$user = Auth::instance()->get_user();

		if ($user)
		{
			return (int) ORM::factory('User_Settings')
						->get_by_name($user->id, Service_Lider::LIDER_SETTING_NAME)
						->find()->value;
		} else {
			return 0;
		}
	}

	static function set_balance($user, $count)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$premium = ORM::factory('User_Settings')
						->get_by_name($user->id, Service_Lider::LIDER_SETTING_NAME)
						->find();

		$premium->user_id = $user->id;
		$premium->name = Service_Lider::LIDER_SETTING_NAME;
		$premium->value = (int) $count;
		$premium->save();

		return $count;
	}

	static function decrease_balance($user, $count = 1)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = self::get_balance($user);

		if ($balance == 0)
			return FALSE;

		return self::set_balance($user, $balance - $count);
	}

	static function increase_balance($user, $count = 1)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = self::get_balance($user);

		if ($balance == 0)
			return FALSE;

		return self::set_balance($user, $balance + $count);
	}
}