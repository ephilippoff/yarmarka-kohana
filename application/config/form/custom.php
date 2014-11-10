<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'orginfo'		=> array(
			"fields" => array(
					"org_name"		 => array("name"=>"org_name"	  ,"translate"=>"Реквизиты организации","type"=>"long","required"=>TRUE),
					"full_org_name" => array("name"=>"full_org_name" ,"translate"=>"Полное название","type"=>"long","required"=>TRUE),
					"INN" 			=> array("name"=>"INN"			  ,"translate"=>"ИНН","required"=>TRUE),
					"KPP" 			=> array("name"=>"KPP"			  ,"translate"=>"КПП","required"=>TRUE),
					"ur_address" 	=> array("name"=>"ur_address" 	  ,"translate"=>"Юридический адрес","type"=>"long","required"=>TRUE),
					"mail_address" 	=> array("name"=>"mail_address"  ,"translate"=>"Почтовый адрес","type"=>"long","required"=>TRUE),
					"logo" 			=> array("name"=>"logo"		  ,"translate"=>"Логотип", "type" => "photo"),
					"official_email" => array("name"=>"official_email","translate"=>"Эл. почта"),
					"www" 			=> array("name"=>"www"			  ,"translate"=>"www"),
					"orgphone" 		=> array("name"=>"orgphone"		  ,"translate"=>"Телефон организациии","required"=>TRUE),
				)
		)
);