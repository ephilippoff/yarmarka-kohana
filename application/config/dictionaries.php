<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	"headhanter_types" => array(
		"agency"   => array( "title" => "Рекрутинговое агентство"),
		"employer" => array( "title" => "Прямой работодатель")
	),
	"org_types" => array(
		1  => "Частное лицо",
		2  => "Компания"
	),
	"org_moderate_states" => array(
		0 => "На проверке",
		1  => "Проверка успешно пройдена",
		2  => "Проверка не пройдена. Исправьте указанные ошибки"
	)
);