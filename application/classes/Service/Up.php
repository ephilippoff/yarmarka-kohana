<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Up extends Service
{
	protected $_name = "up";
	protected $_title = "Подъем";
	protected $_is_multiple = FALSE;
	protected static $_free_count = 1;

	public function __construct($object_id = NULL)
	{
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

		$user =  Auth::instance()->get_user();
		$last_freeup = ORM::factory('User_Settings')->get_by_name($user->id, 'freeup_date')->find()->value;

		return array(
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $price,
			"quantity" => $quantity,
			"discount" => $discount,
			"discount_name" => $discount_name,
			"discount_reason" => $discount_reason,
			"price_total" => $price_total,
			"description" => $description,
			"last_freeup" => ($user AND $last_freeup) ? $last_freeup : FALSE,
			"next_freeup" => ($user AND $last_freeup) ? date('d.m.Y H:i', strtotime('+10 days', strtotime($last_freeup))) : FALSE
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

		if ($orderItem->service->discount_name == "free_up") {
			$order = ORM::factory('Order', $orderItem->order_id);
			if ($order->user_id) {
				ORM::factory('User_Settings')->freeup_save($order->user_id);
				ORM::factory('User_Settings')->freeup_remove($order->user_id, 'freeup_reserve');
			}
		}

		if ($quantity > 1) {
			ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги Подъем * %s: № %s", array( $quantity, $orderItem->order_id ) ) );
		}
	}

	static function apply_service($object_id, $quantity, $auto_activated = FALSE)
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

			$or->activated = $or->activated + 1;
			
			if (!$auto_activated) {
				$or->count = $or->count + $quantity;
			}

		} else {
			$or->count = $quantity;
		}
		
		$or->date_created =  DB::expr("NOW()");
		$or->object_id = $object_id;
		$or->save();

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
			$freeup_reserve = ORM::factory('User_Settings')->get_by_name($user->id, 'freeup_reserve')->find();

			return ($freeup_reserve->loaded()) ? 0 : ORM::factory('User_Settings')->freeup_exists($user->id);
		} else {
			return 0;
		}
	}

	static function decrease_balance($user, $count = 1)
	{
		ORM::factory('User_Settings')->freeup_save($user->id, 'freeup_reserve');
	}

	static function increase_balance($user, $count = 1)
	{
		ORM::factory('User_Settings')->freeup_remove($user->id, 'freeup_reserve');
	}
}