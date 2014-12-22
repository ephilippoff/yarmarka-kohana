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
	),
	"org_moderate_decline" => array(
		0  => "Вы прикрепили ИНН, принадлежащий другой компании",
		1  => "Приложенное фото, не является копией свидетельства ИНН компании"
	),
	"user_setting_types" => array(
		"orginfo"  => "Информация о организации",
		"userinfo" => "Информация о пользователях",
		"massload" => "Массовая загрузка",
		"objects"  => "Услуги к объявлениям (premium, auto_up, percent_up)"
	),
	"estimate_for_org_info" => array(
		"0_trash"  => "Деятельность компании сомнительна",
		"50_normal" => "Не внимательно/как попало заполнена анкета",
		"75_nologo" => "Нет логотипа",
		"100_perfect" => "Идеально"
	)
);