<?php defined('SYSPATH') OR die('No direct script access.');

class Dbhelper 
{

	/**
	 * convert_pg_array
	 *
	 * for postgre array column (http://www.postgresql.org/docs/8.2/static/arrays.html) database class return string like '{66, 255}'
	 * this function convert this string to php arrray {[0] => 66, [1] => 255}
	 * 
	 * @param string $array_string 
	 * @static
	 * @access public
	 * @return array
	 */
	public static function convert_pg_array($array_string)
	{
		$str =trim(str_replace(array('{', '}'), '', $array_string));
		
		if ($str)
		{
			return explode(',', $str);
		}
		else
		{
			return array();
		}

	}
}
