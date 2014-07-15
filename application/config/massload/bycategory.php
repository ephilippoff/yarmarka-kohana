<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'kvartiry-i-komnaty'		=> array
	(
		'id' 			=> array('name' => 'id',		'required' => TRUE, 'type' => 'text', 		'translate' => 'ID'),
		'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город'),
		'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес'),
		'tip-sdelki5' 	=> array('name' => 'tip-sdelki5','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки'), 
		'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат'),
		'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь'),	
		'etazh' 		=> array('name' => 'etazh',	'required' => FALSE, 	'type' => 'integer', 	'translate' => 'Этаж'),	
		'etazhnost' 	=> array('name' => 'etazhnost',	'required' => FALSE, 'type' =>'integer', 	'translate' => 'Этажность'),			
		'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена'),
		'user_text_adv' => array('name' => 'user_text_adv','required' => FALSE,'type' => 'textadv', 'translate' => 'Текст объявления'),
		'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1'),
		'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2'),
		'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО'),
		'image' 		=> array('name' => 'image',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото')
	)

);