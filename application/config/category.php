<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	"23" => array( //автозапчасти
		"price_enabled" => TRUE
	),
	"26" => array( //аренда авто
		"price_enabled" => TRUE
	),
	"29" => array( //грузопассажирские перевозки
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"36" => array( //вакансии
		"description" => "vakancy_law",
		"additional_fields" => array(
			1 => array("additional_org_name", "additional_vakancy_org_type", "additional_commoninfo"),
			2 => array("additional_vakancy_org_type"),
		),
		"additional_saveas" => array(
			"additional_org_name" => array("param_256","Компания")
		)
	),
	"41" => array( //обучение курсы
		"price_enabled" => TRUE
	),
	"42" => array( //медицина и здоровье
		"price_enabled" => TRUE
	),
	"43" => array( //Юридические услуги
		"price_enabled" => TRUE
	),
	"44" => array( //финансы и аудит
		"price_enabled" => TRUE
	),
	"45" => array( //красота и здоровье
		"price_enabled" => TRUE
	),
	"49" => array(//"Ремонт и обслуживание бытовой техники"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"50" => array(//"Ремонт квартир и офисов"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"51" => array(//"Ремонт и обслуживание компьютеров"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"52" => array( //установка и изготовление на заказ
		"price_enabled" => TRUE
	),
	"53" => array(//"Услуги сантехника"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"54" => array( //строиельные работы
		"price_enabled" => TRUE
	),
	"56" => array(//"Услуги электрика"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"57" => array(//"Прочие услуги по ремонту"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"58" => array( //инструменты и инвентарь
		"price_enabled" => TRUE
	),
	"59" => array( //оборудование для бизнеса
		"price_enabled" => TRUE
	),
	"60" => array( //мебель и интерьер
		"price_enabled" => TRUE
	),
	"75" => array( //Другие предмееты потребелния
		"price_enabled" => TRUE
	),
	"113" => array( //велосипеды
		"price_enabled" => TRUE
	),
	"126" => array( //строит и отделочные материалы
		"price_enabled" => TRUE
	),
	"133" => array( //репетиторство
		"price_enabled" => TRUE
	),
	"134" => array( //бизнес образование
		"price_enabled" => TRUE
	),	
	"139" => array( //водный транспорт
		"price_enabled" => TRUE
	),
	"142" => array( //малый коммерческий транспорт
		"price_enabled" => TRUE
	),
	"143" => array( //Спецтехника
		"price_enabled" => TRUE
	),
	"146" => array( //Тракторы
		"price_enabled" => TRUE
	),
	"154" => array(//"Ремонт и обслуживание оргтехники"
		"one_mobile_phone" => TRUE,
		"max_count" => 1,
		"description" => "one_advert_category",
		"price_enabled" => TRUE
	),
	"155" => array( //каталог компаний
		"price_enabled" => TRUE
	),
	'categories_view_hidden' => array('modulnaya-reklama', 'novosti')//исключение категорий из рендера меню
);