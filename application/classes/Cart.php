<?php defined('SYSPATH') OR die('No direct script access.');

class Cart {

	static function get_key($string =  "")
	{
		$session_id = session_id();

		$key = Cookie::get("cartKeyS");
		if (!$key)
		{
			$key = sha1($string.$session_id. microtime() .  md5($session_id . microtime() . 'sdf97xc65bvx8ckl;l;jsdf7vb45##!&&1'));
			Cookie::set("cartKeyS", $key, strtotime( '+90 days' ));
		}

		return $key;
	}

	static function get_info()
	{
		if (!Cookie::get("cartKeyS"))
		{
			return array(
				"count" => 0,
				"summ" => 0
			);
		}

		$key = Cart::get_key();

		$order_count = ORM::factory('Order_ItemTemp')
						->where("key","=",$key)->count_all();

		return array(
				"count" => $order_count,
				"summ" => 0
		);
	}

	static function clear($key, $clear_cookie = TRUE)
	{
		if ($clear_cookie) {
			Cookie::set('cartKeyS', NULL, -1);
		}
		ORM::factory('Order_ItemTemp')->where("key", "=", $key)->delete_all();
	}
}