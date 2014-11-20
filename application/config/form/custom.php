<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'orginfo'		=> array(
			"fields" => array(
					"full_org_name" => array("name"=>"full_org_name" ,"translate"=>"Полное название","type"=>"long","required"=>TRUE,
												"description" => "Например : ООО 'Рога и копыта'"),
					"INN" 			=> array("name"=>"INN"			  ,"translate"=>"ИНН","required"=>TRUE),
					"INN_photo" 	=> array("name"=>"INN_photo"	,
											 "translate"=>"Скан ИНН", 
											 "type" => "photo",
											 "required" => TRUE,
											 "description" => "оригинал или копия",
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
					"phonetech" 	=> array("name"=>"phonetech"		  ,"translate"=>"Телефон 2","required"=>TRUE,
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