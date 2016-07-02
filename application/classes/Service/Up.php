<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Up extends Service
{
	protected $_name = "up";
	protected $_title = "Подъем";
	protected $_is_multiple = FALSE;
	protected static $_free_count = 1;

	public function __construct($object_id = NULL)
	{
		if ($object_id) {
			$object = ORM::factory('Object',$object_id);
			if ($object->loaded()) {
				$this->object($object);
			}
		}

		$this->_initialize();
	}

	public function get()
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$available = $this->check_available($quantity);
		if ($available) {
			$discount = $price * $quantity;
			$discount_reason = " (бесплатный)";
			$discount_name = "free_up";
		}
		$price_total = $price * $quantity - $discount;
		$description = $this->get_params_description().$discount_reason;

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
		return "Количество: ".(($this->quantity()) ? $this->quantity() : 1);
	}

	public function apply($orderItem)
	{
		$quantity = $orderItem->service->quantity;

		self::apply_service($orderItem->object->id, $quantity);
		self::saveServiceInfoToCompiled($orderItem->object->id);

		if ($quantity > 1) {
			ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги Подъем * %s: № %s", array( $quantity, $orderItem->order_id ) ) );
		}
	}

	static function apply_service($object_id, $quantity)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;
		
		$object->date_created = DB::expr("NOW()");
		
		if ( strtotime( $object->date_expiration ) < strtotime( Lib_PlacementAds_AddEdit::lifetime_to_date("45d") ) ) {
			
			$object->date_expiration = Lib_PlacementAds_AddEdit::lifetime_to_date("45d");

		}

		$object->save();



		$or = ORM::factory('Object_Service_Up')
					->where("object_id", "=", $object_id)
					->find();
		if ($or->loaded())
		{
			$or->activated = $or->activated + $quantity;
		} else {
			$or->count = $quantity;
		}
		$or->object_id = $object_id;
		$or->save();

		return TRUE;
	}

	public function check_available($quantity, $balance = FALSE)
	{
		$result = FALSE;
		$quantity = ($quantity) ? $quantity : 1;
		if ($quantity > 1) return $result;
		
		$object = $this->object();
		if (!$object) return $result;

		$balance = ($balance) ? $balance : self::get_balance($object->id);


		if (!isset($balance)) {
			$user = Auth::instance()->get_user();

			$count = 0;
			if ($user AND $user->id  == $object->author ) {
				$count = Service_Up::$_free_count;
			}

			$balance = (int) self::set_balance($object->id, $count, $object->category);
		}

		if ($balance >= 0 AND $balance - intval($quantity) >= 0) {
			return TRUE;
		}

		return $result;
	}

	static function get_balance($object_id = NULL)
	{
		if (!$object_id) return;

		if ($object_id)
		{
			return Cache::instance("services")->get("up:".$object_id);
		} else {
			return 0;
		}
	}

	static function set_balance($object_id, $count)
	{
		if (!$object_id) return;

		$object = ORM::factory('Object', $object_id);

		$duration = Date::TWOWEEKS;

		if ($object->category == 36) {
			$duration = Date::THREEDAYS;
		}

		Cache::instance("services")->set("up:".$object_id, (int) $count, $duration);

		return $count;
	}

	static function decrease_balance($object_id, $count = 1)
	{
		if (!$object_id) return;

		$balance = self::get_balance($object_id);

		if ($balance == 0)
			return FALSE;

		return self::set_balance($object_id, $balance - $count);
	}

	static function increase_balance($object_id, $count = 1)
	{
		if (!$object_id) return;

		$balance = self::get_balance($object_id);

		return self::set_balance($object_id, $balance + $count);
	}
}