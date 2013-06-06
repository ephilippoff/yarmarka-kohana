<?php defined('SYSPATH') or die('No direct script access.');

class Num extends Kohana_Num {

	public static function price($price)
	{
		return number_format($price, 0 , ',', ' ');
	}
}
