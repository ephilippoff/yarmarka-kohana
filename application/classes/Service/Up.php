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

	public function get($category)
	{
		$quantity = ($this->quantity()) ? $this->quantity() : 1;
		$price = $price_total = $this->getPrice();
		$discount = 0;
		$discount_reason = "";
		$discount_name = FALSE;
		$available = $this->check_available($quantity, $category);
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

	public function check_available($quantity, $category = 0, $balance = FALSE)
	{
		$result = FALSE;
		$quantity = ($quantity) ? $quantity : 1;
		if ($quantity > 1) return $result;
		
		$user = Auth::instance()->get_user();
		if (!$user) return $result;

		$balance = ($balance) ? $balance : self::get_balance($category, $user);
		if (!isset($balance)) {
			if ( Acl::check("object.add.type") )
			{
				$balance = (int) self::set_balance($user, 500, $category);
			} else {
				$balance = (int) self::set_balance($user, Service_Up::$_free_count, $category);
			}
		}

		if ($balance >= 0 AND $balance - intval($quantity) >= 0) {
			return TRUE;
		}

		return $result;
	}

	static function get_balance($category = 0, $user = NULL)
	{
		if (!$user) 
			$user = Auth::instance()->get_user();

		$parent_category = ORM::factory('Category')->where("id","=", $category)->find()->parent_id;

		if ($user)
		{
			if ($category == 16 OR $parent_category == 16) {
				return Cache::instance("services")->get("up:".$user->id."-".$parent_category);
			}else {
				return Cache::instance("services")->get("up:".$user->id);
			}
		} else {
			return 0;
		}
	}

	static function set_balance($user, $count, $category)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$parent_category = ORM::factory('Category')->where("id","=", $category)->find()->parent_id;

		if ($category == 16 OR $parent_category == 16) {
			Cache::instance("services")->set("up:".$user->id."-".$parent_category, (int) $count, Date::DAY*3);
			Cache::instance("services")->set("up:".$user->id."-".$parent_category.'-date', (string) date("Y-m-d, H:i", time() + Date::DAY*3), Date::DAY*3);

		}else {
			Cache::instance("services")->set("up:".$user->id, (int) $count, Date::WEEK*2);
			Cache::instance("services")->set("up:".$user->id.'-date', (string) date("Y-m-d, H:i", time() + Date::WEEK*2), Date::WEEK*2);
		}

		return $count;
	}

	static function get_date($category = 0, $user = NULL)
	{
		if (!$user) 
			$user = Auth::instance()->get_user();

		$parent_category = ORM::factory('Category')->where("id","=", $category)->find()->parent_id;

		if ($user)
		{
			if ($category == 16 OR $parent_category == 16) {
				return Cache::instance("services")->get("up:".$user->id."-".$parent_category.'-date');
			}else {
				return Cache::instance("services")->get("up:".$user->id.'-date');
			}
		} else {
			return 0;
		}
	}

	static function decrease_balance($user, $count = 1, $category_id)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = self::get_balance($category_id, $user);

		if ($balance == 0)
			return FALSE;

		return self::set_balance($user, $balance - $count, $category_id);
	}

	static function increase_balance($user, $count = 1, $category_id)
	{
		if (!$user)
			$user = Auth::instance()->get_user();

		$balance = self::get_balance($category_id, $user);

		return self::set_balance($user, $balance + $count, $category_id);
	}
}