<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Up extends Service
{
	protected $_name = "up";
	protected $_title = "Подъем";
	protected $_is_multiple = FALSE;
	protected static $_free_count = 3;

	public function __construct($object_id = NULL)
	{
		$this->_initialize();
	}

	public function get($params = array())
	{

		$params = new Obj($params);
		$quantity = $params->quantity = ($params->quantity) ? $params->quantity : 1;
		$price = $price_total = $this->getPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$params->available = $this->check_available($quantity);
		if ($params->available) {
			$discount = $price * $quantity;
			$discount_reason = " (бесплатный)";
			$discount_name = "free_up";
		}
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description($params).$discount_reason;

		return array(
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
		return "Количество: ".$params->quantity;
	}

	public function apply($orderItem)
	{
		self::apply_service($orderItem->object->id);
		self::saveServiceInfoToCompiled($orderItem);
	}

	static function apply_service($object_id)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;
		
		$object->date_created = DB::expr("NOW()");
		$object->save();

		$or = ORM::factory('Object_Service_Up')
					->where("object_id", "=", $object_id)
					->find();

		$or->object_id = $object_id;
		$or->save();

		return TRUE;
	}

	public static function check_available($quantity, $balance = FALSE)
	{
		$result = FALSE;
		$quantity = ($quantity) ? $quantity : 1;
		if ($quantity > 1) return $result;
		
		$user = Auth::instance()->get_user();
		if (!$user) return $result;

		$balance = ($balance) ? $balance : self::get_balance($user);
		if (!isset($balance)) {
			$balance = (int) self::set_balance($user, Service_Up::$_free_count);
		}

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
			return Cache::instance("services")->get("up:".$user->id);
		} else {
			return 0;
		}
	}

	static function set_balance($user, $count)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		Cache::instance("services")->set("up:".$user->id, (int) $count, Date::DAY);
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

		return self::set_balance($user, $balance + $count);
	}
}