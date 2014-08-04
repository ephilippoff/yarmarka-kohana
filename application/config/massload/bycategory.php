<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'flat_resale'		=> array( 
			'id'    => 3,
			'name'	=> 'Продажа квартир и комнат (вторичное жилье)',
			'category' =>'flat_resale',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 30),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki5' 	=> array('name' => 'tip-sdelki5','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат', 'maxlength' => 30),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => FALSE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => FALSE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 30),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 30),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 3, //категория квартиры и комнаты
						'param_441' => 3196, //вторичное жилье
					)
	),
	'flat_new'		=> array( 
			'id'    => 3,
			'name'	=> 'Продажа квартир и комнат (новостройки)',
			'category' =>'flat_new',
			'fields'=>array(),
			'autofill'=>array()					
					),
	'house'		=> array( 
			'id'    => 30,
			'name'	=> 'Дома, дачи, коттеджи',
			'category' =>'house',
			'fields'=>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 30),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'dacha-chastnyi-dom' 	=> array('name' => 'dacha-chastnyi-dom','required' => TRUE, 'type' => 'dict', 'translate' => 'Вид объекта', 'maxlength' => 30),
						'ploshchad-doma' 		=> array('name' => 'ploshchad-doma',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь дома', 'maxlength' => 3),	
						'ploshchad-uchastka-v-sotkakh' 		=> array('name' => 'ploshchad-uchastka-v-sotkakh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь участка', 'maxlength' => 3),	
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 30),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 30),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 30,
				)					
					),
	'land'		=> array( 
			'id'    => 34,
			'name'	=> 'Земельные участки',
			'category' =>'land',
			'fields'=>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 30),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'kategoriya-zemli' 	=> array('name' => 'kategoriya-zemli','required' => TRUE, 'type' => 'dict', 'translate' => 'Категория земли', 'maxlength' => 30),
						'ploshchad-uchastka-v-sotkakh' 		=> array('name' => 'ploshchad-uchastka-v-sotkakh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь участка', 'maxlength' => 3),	
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 30),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 30),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 34,
				)					
					),


);