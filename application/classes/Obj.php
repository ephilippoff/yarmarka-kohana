<?php defined('SYSPATH') or die('No direct script access.');

class Obj {

	function __construct($array = Array())
	{
		foreach ($array as $key=>$value)
			$this->{$key} = $value;
	}

	function __get($key)
	{
		if (property_exists($this, $key))
			return $key;
		else
			return NULL;
	}

	function __toString(){
		return (string) var_dump(get_object_vars($this));
	}

	function count(){
		return count(get_object_vars($this));
	}
}
