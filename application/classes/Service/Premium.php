<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Premium extends Service
{
	const PREMIUM_SETTING_NAME = 'premium';
	const PREMIUM_DAYS = 7;

	protected $_title = "Премиум";
	protected $_name = "premium";
	protected $_is_multiple = TRUE;

	public function __construct()
	{
		$this->_initialize();
	}

	public function get()
	{

		return array(
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => ($this->_is_multiple) ? $this->getPriceMultiple() : $this->getPrice()
		);
	}

	public function apply($orderItem)
	{
		Service_Premium::apply_service($orderItem->object_id);
		self::saveServiceInfoToCompiled($orderItem);
	}

	static function apply_prepayed($object_id, $city_id = NULL, $user = NULL)
	{
		if (Service_Premium::get_balance($user)<=0)
			return FALSE;
		
		Service_Premium::apply_service($object_id, $city_id);
		return Service_Premium::decrease_balance($user);
	}

	static function get_balance($user)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		return (int) ORM::factory('User_Settings')
						->get_by_name($user->id, Service_Premium::PREMIUM_SETTING_NAME)
						->find()->value;
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

	static function decrease_balance($user)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = Service_Premium::get_balance($user);

		if ($balance == 0)
			return FALSE;

		return Service_Premium::set_balance($user, $balance-1);
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

	static function apply_service($object_id, $city_id = NULL, $user = NULL)
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
		$or->object_id = $object_id;
		$or->city_id = $city_id;
		$or->date_expiration = DB::expr("(NOW() + INTERVAL '".Service_Premium::PREMIUM_DAYS." days')");
		$or->save();

		$object->date_created = DB::expr("NOW()");
		$object->save();

		return TRUE;
	}

}