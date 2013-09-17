<?php defined('SYSPATH') OR die('No direct script access.');

class Valid extends Kohana_Valid {
	/**
	 * Checks if a field is not 0
	 *
	 * @return  boolean
	 */
	public static function not_0($value)
	{
		return intval($value) !== 0;
	}
}
