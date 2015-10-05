<?php defined('SYSPATH') OR die('No direct script access.');

class Cookie extends Kohana_Cookie {

	public static function set($name, $value, $expiration = NULL)
	{
		$main_domain = Kohana::$config->load('common.main_domain');

		if ($expiration === NULL)
		{
			// Use the default expiration
			$expiration = Cookie::$expiration;
		}

		if ($expiration !== 0)
		{
			// The expiration is expected to be a UNIX timestamp
			$expiration += time();
		}

		// Add the salt to the cookie value
		$value = Cookie::salt($name, $value).'~'.$value;

		return setcookie($name, $value, $expiration, Cookie::$path, ".".$main_domain, Cookie::$secure, Cookie::$httponly);
	}

	public static function dset($name, $value, $expiration = NULL)
	{
		$main_domain = Kohana::$config->load('common.main_domain');

		if ($expiration === NULL)
		{
			// Use the default expiration
			$expiration = Cookie::$expiration;
		}

		if ($expiration !== 0)
		{
			// The expiration is expected to be a UNIX timestamp
			$expiration += time();
		}

		// Add the salt to the cookie value
		//$value = Cookie::salt($name, $value).'~'.$value;

		return setcookie($name, $value, $expiration, Cookie::$path, ".".$main_domain, Cookie::$secure, Cookie::$httponly);
	}

	public static function dget($key, $default = NULL)
	{
		if ( ! isset($_COOKIE[$key]))
		{
			// The cookie does not exist
			return $default;
		}

		// Get the cookie value
		$cookie = $_COOKIE[$key];

		// Find the position of the split between salt and contents
		$split = strlen(Cookie::salt($key, NULL));

		return $cookie;
	}

}