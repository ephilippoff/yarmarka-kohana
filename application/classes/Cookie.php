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

	public static function save_toobject_history($object_id)
	{
		$cookie_name = 'ohistory';
		$ohistory_max_count = 30;
		
		$objects_stack = ($cookie = trim(self::get($cookie_name))) ? explode(',', $cookie) : array();

		//удаляем добавляемое значение, если оно уже есть в стеке(исключаем дубли и "освежаем" позицию в стеке)
		if (($key = array_search($object_id, $objects_stack)) !== false)
			unset($objects_stack[$key]);
		//наполняем стек пока не достигнут лимит, удаляем последний элемент в стеке
		if (count($objects_stack) >= $ohistory_max_count)
			array_pop($objects_stack);
		//новое значение в начало стека	
		array_unshift($objects_stack, $object_id);
		//запоминаем
		self::set($cookie_name, implode(',', $objects_stack), strtotime( '+180 days' ));
	}

}