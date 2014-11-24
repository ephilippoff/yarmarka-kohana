<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'orginfo'		=> array(
			"fields" => array(
					"org_name" => array("name"=>"org_name" ,"translate"=>"Название","type"=>"long","required"=>TRUE,
												"description" => "Например : Рога и копыта"),
					"org_full_name" => array("name"=>"org_full_name" ,"translate"=>"Юридическое название","type"=>"long","required"=>TRUE,
												"description" => "Например : ООО 'Рога и копыта'. Как указано в свидетельстве ИНН"),
					"INN" 			=> array("name"=>"INN"			  ,
												"translate"=>"ИНН",
												"description" => "ИНН не публикуется",
												"required"=>TRUE,
												"type" => "inn"),
					"INN_photo" 	=> array("name"=>"INN_photo"	,
											 "translate"=>"Фото или скан ИНН", 
											 "type" => "photo",
											 "required" => TRUE,
											 "description" => "Оригинал или копия. Скан ИНН не публикуется",
											 "size" => "orig"),
					"mail_address" 	=> array("name"=>"mail_address"  ,"translate"=>"Почтовый адрес","type"=>"long","required"=>TRUE,
												"description" => "Формат: Город, улица, дом, офис"),
					"logo" 			=> array("name"=>"logo"		  ,"translate"=>"Логотип", "type" => "photo","size" => "272x203"),
					"official_email"=> array("name"=>"official_email","translate"=>"E-mail"),
					"www" 			=> array("name"=>"www"			  ,"translate"=>"Адрес сайта"),
					"vkontakte" 	=> array("name"=>"vkontakte", "translate"=>"Группа Вконтакте"),
					"twitter" 		=> array("name"=>"twitter", "translate"=>"Адрес Twitter"),
					"instragram" 	=> array("name"=>"instragram", "translate"=>"Instagram"),
					"contact" 		=> array("name"=>"contact"		  ,"translate"=>"Контактное лицо","required"=>TRUE),
					"phone" 		=> array("name"=>"phone"		  ,"translate"=>"Телефон","required"=>TRUE,
											 "description" => "Официальный телефон организации"),
					"phonetech" 	=> array("name"=>"phonetech"		  ,"translate"=>"Телефон 2",
											 "description" => "Для связи с нами по техническим и другим вопросам"),
					"commoninfo" 	=> array(	"name"=>"commoninfo",
												"translate"=>"О компании",
												"required" => TRUE, 
												"type" => "text",
												"description" => "заполните краткую рекламную информацию о дейтельности компании"),
				)
		),
	'userinfo'		=> array(
			"fields" => array(
					"contact_name"  => array("name"=>"contact_name" ,
											 "translate"=>"Контакное лицо/ФИО", 
											 "type"=>"long",
											 "description" => "Будет подставляться в качестве контактного лица, в объявлении"),
					"www" 			=> array("name"=>"www"			  ,"translate"=>"Адрес сайта"),
					"vkontakte" 	=> array("name"=>"vkontakte", "translate"=>"Группа Вконтакте"),
					"twitter" 		=> array("name"=>"twitter", "translate"=>"Адрес Twitter"),
					"instragram" 	=> array("name"=>"instragram", "translate"=>"Instagram"),
					"phone" 		=> array("name"=>"phone"		  ,"translate"=>"Телефон"),
					"commoninfo" 		=> array(	"name"=>"commoninfo",
													"translate"=>"Общая информация", 
													"type" => "text"
													),
				)
		)
);