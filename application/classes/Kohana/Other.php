<?php defined('SYSPATH') OR die('No direct access');

class Kohana_Other {

	public static function get_controllers($array)
	{
		$list_controllers = array();
		foreach ($array as $name => $controller) 
		{
			if (is_array($controller)){

				$parent_name = basename($name,'.php');
				$list_controllers[] = $parent_name;
				$childs = array();
				foreach (self::get_controllers($controller) as $child_value) {
					$childs[] = $parent_name."_".$child_value;
				}
				$list_controllers = array_merge($list_controllers, $childs);
			} else {
				$controller = basename($controller,'.php');
				$list_controllers[] = $controller;
			}
		}

		return $list_controllers;
	}
}
