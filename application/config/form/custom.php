<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'orginfo'		=> array(
			"fields" => array(
					"org_name" => array("name"=>"org_name" ,"translate"=>"Название","type"=>"long","required"=>TRUE,
												"description" => "Например : Рога и копыта", "length" => 255),
					"org_full_name" => array("name"=>"org_full_name" ,"translate"=>"Юридическое название","type"=>"long","required"=>TRUE,
												"description" => "Например : ООО 'Рога и копыта'. Как указано в свидетельстве ИНН", "length" => 255),
					"INN" 			=> array("name"=>"INN"			  ,
												"translate"=>"ИНН",
												"description" => "ИНН не публикуется",
												"required"=>TRUE,
												"type" => "inn", "length" => 30),
					"INN_photo" 	=> array("name"=>"INN_photo"	,
											 "translate"=>"Фото или скан ИНН", 
											 "type" => "photo",
											 "required" => TRUE,
											 "description" => "Необходимо загрузить оригинал или копию свидетельства ИНН. Свидетельство не будет опубликовано.",
											 "size" => "orig", "length" => 255),
					"mail_address" 	=> array("name"=>"mail_address"  ,"translate"=>"Почтовый адрес","type"=>"long","required"=>TRUE,
												"description" => "Формат: Город, улица, дом, офис", "length" => 255),
					"logo" 			=> array("name"=>"logo"		  ,"translate"=>"Логотип", "type" => "photo","size" => "272x203", "length" => 255),
					"official_email"=> array("name"=>"official_email","translate"=>"E-mail"),
					"www" 			=> array("name"=>"www"			  ,"translate"=>"Адрес сайта"),
					"vkontakte" 	=> array("name"=>"vkontakte", "translate"=>"Группа Вконтакте"),
					"twitter" 		=> array("name"=>"twitter", "translate"=>"Адрес Twitter"),
					"instragram" 	=> array("name"=>"instragram", "translate"=>"Instagram"),
					"contact" 		=> array("name"=>"contact"		  ,"translate"=>"Контактное лицо","required"=>TRUE),
					"phone" 		=> array("name"=>"phone"		  ,"translate"=>"Телефон","required"=>TRUE,
											 "description" => "Официальный телефон организации", "length" => 255),
					"phonetech" 	=> array("name"=>"phonetech"		  ,"translate"=>"Телефон 2",
											 "description" => "Для связи с нами по техническим и другим вопросам"),
					"commoninfo" 	=> array(	"name"=>"commoninfo",
												"translate"=>"О компании",
												"required" => TRUE, 
												"type" => "text",
												"description" => "Заполните краткую рекламную информацию о деятельности компании"),
				)
		),
	'userinfo'		=> array(
			"fields" => array(
					"contact_name"  => array("name"=>"contact_name" ,
											 "translate"=>"Контакное лицо/ФИО", 
											 "type"=>"long",
											 "description" => "Будет подставляться в качестве контактного лица, в объявлении", "length" => 255),
					"www" 			=> array("name"=>"www"			  ,"translate"=>"Адрес сайта"),
					"vkontakte" 	=> array("name"=>"vkontakte", "translate"=>"Группа Вконтакте"),
					"twitter" 		=> array("name"=>"twitter", "translate"=>"Адрес Twitter"),
					"instragram" 	=> array("name"=>"instragram", "translate"=>"Instagram"),
					"phone" 		=> array("name"=>"phone"		  ,"translate"=>"Телефон", "length" => 255),
					"commoninfo" 		=> array(	"name"=>"commoninfo",
													"translate"=>"Общая информация", 
													"type" => "text"
													),
				)
		)
);