<?php defined('SYSPATH') OR die('No direct script access.');


class Service_Premium extends Service
{
	const PREMIUM_SETTING_NAME = 'premium';
	const PREMIUM_DAYS = 7;

	protected $_title = "Премиум";
	protected $_name = "premium";
	protected $_is_multiple = TRUE;

	public function __construct($object_id = NULL)
	{
		$object = ORM::factory('Object',$object_id);
		if ($object->loaded()) {
			$this->object($object);
		}
		$this->_initialize();
	}

	public function get($params = array())
	{
		$params = new Obj($params);
		$quantity = $params->quantity = ($params->quantity) ? $params->quantity : 1;
		$price = $price_total = $this->getPriceMultiple();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$params->available = $this->check_available($quantity);
		if ($params->available) {
			$discount = $price * $quantity;
			$discount_reason = " (предоплаченный)";
			$discount_name = "prepayed_premium";
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

	public function get_params_description($params)
	{
		return "Количество: ".$params->quantity;
	}

	/**
	 * [apply Apply payd service Premium to object.]
	 * @param  [type] $orderItem [description]
	 * @return [void]
	 */
	public function apply($orderItem)
	{
		$quantity = $orderItem->service->quantity;

		self::apply_service($orderItem->object->id, $quantity);
		self::saveServiceInfoToCompiled($orderItem);
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
						->get_by_name($user->id, Service_Premium::PREMIUM_SETTING_NAME)
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
						->get_by_name($user->id, Service_Premium::PREMIUM_SETTING_NAME)
						->find();
		$premium->user_id = $user->id;
		$premium->name = Service_Premium::PREMIUM_SETTING_NAME;
		$premium->value = (int) $count;
		$premium->save();

		return $count;
	}

	static function decrease_balance($user, $count = 1)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = Service_Premium::get_balance($user);

		if ($balance == 0)
			return FALSE;

		return Service_Premium::set_balance($user, $balance - $count);
	}

	static function increase_balance($user, $count = 1)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = Service_Premium::get_balance($user);

		if ($balance == 0)
			return FALSE;

		return Service_Premium::set_balance($user, $balance + $count);
	}

	static function get_already_buyed($user)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$result = Array();
		$buyed = ORM::factory('Object_Rating')
					->join('object', 'left')
						->on('object.id', '=', 'object_id')
					->where("object.author","=", $user->id)
					->find_all();

		foreach ($buyed as $object_rating) {
			$result[$object_rating->object_id] = array(
					"city_id" 			=> $object_rating->city_id,
					"date_expiration" 	=> $object_rating->date_expiration
				);
		}
		return $result;
	}

	static function is_already_buyed($object_id)
	{
		if (!$object_id)
			return FALSE;

		$result = Array();
		$buyed = ORM::factory('Object_Rating')
					->join('object', 'left')
						->on('object.id', '=', 'object_id')
					->where("object.id","=", $object_id)
					->where("object_rating.date_expiration",">",DB::expr("NOW()"))
					->find();

		return $buyed->loaded();
	}

	static function apply_service($object_id, $quantity, $city_id = NULL, $user = NULL)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;

		if (!$city_id) 
			$city_id = $object->city_id;

		if (!$user)
			$user = Auth::instance()->get_user();

		$or = ORM::factory('Object_Rating')
					->where("object_id", "=", $object_id)
					->where("city_id", "=", $city_id)
					->find();
		if ($or->loaded())
		{
			$quantity += $or->count - 1;
		}
		$or->count = $quantity;
		$or->object_id = $object_id;
		$or->city_id = $city_id;
		$or->date_expiration = DB::expr("(NOW() + INTERVAL '".Service_Premium::PREMIUM_DAYS." days')");
		$or->save();

		return TRUE;
	}

}