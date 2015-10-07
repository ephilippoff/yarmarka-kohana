<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	"settings" => array(

	),
	"cl" => array(
		"object" => array (
			"control" => array("auth", "owner"),
			"edit" => array(1,3,9, "auth", "owner"),
			"moderate" => array(1,3,9, "auth"),
			"add" => array(
				"type" => array(1,3,9, "auth")
			)
		),
		"pay_service" => array("auth", 1),
		"news" => array(
			"edit" => array("auth", 1, 3)
		),
		"profiler" => array("auth", 1),
		"kupon" => array("owner", 1, 9)
	)
);