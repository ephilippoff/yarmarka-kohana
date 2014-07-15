<?php defined('SYSPATH') or die('No direct script access.');

class Message {

	public static function get($file, $path, $parameters = Array())
	{
		$message = Kohana::message($file, $path);
		
		$values = Array();
		foreach($parameters as $key=>$value)
			$values[":".$key] = $value;

		return strtr($message, $values);
	}
}
