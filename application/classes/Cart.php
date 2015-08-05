<?php defined('SYSPATH') OR die('No direct script access.');

class Cart {

	static function get_key($string =  "")
	{
		$session_id = session_id();

		$key = Cookie::get("cartKey");
		if (!$key)
		{
			$key = sha1($string.$session_id. microtime() .  md5($session_id . microtime() . 'sdf97xc65bvx8ckl;l;jsdf7vb45##!&&1'));
			Cookie::set("cartKey", $key, strtotime( '+90 days' ));
		}

		return $key;
	}
}