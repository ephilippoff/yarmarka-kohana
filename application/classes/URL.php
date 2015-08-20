<?php defined('SYSPATH') OR die('No direct script access.');

class URL extends Kohana_URL {

	/**
	 * Adds http:// to url if not exists
	 *
	 * @param  string $str url
	 * @return string
	 */
	public static function prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			return '';
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}

		return $str;
	}

	public static function SERVER($name)
	{
		return (@$_SERVER[$name]) ? $_SERVER[$name]: "";
	}
}