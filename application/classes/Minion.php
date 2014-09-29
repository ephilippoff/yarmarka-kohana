<?php defined('SYSPATH') OR die('No direct script access.');

class Minion extends Kohana_Minion_CLI {

	public static function write($pre = '', $text = '')
	{
		parent::write(Minion::prefix_log($pre).$text);
	}

	public static function prefix_log($txt)
	{
		return '['.date("d-m-Y H:i:s").' '.$txt.']: ';
	}
}