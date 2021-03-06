<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'auto'		=> array( 
			'id'    => 15,
			'name'	=> 'Легковые автомобили',
			'category' =>'auto',
			'filter'=>array(),
			'autofill'=>array(
						'rubricid' => 15, //категория легковые автомобили
						'param_466' => 3238, //продажа
					),
			'fields'  =>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),

						'marka6' 		=> array('name' => 'marka6', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Марка', 'maxlength' => 30),
						'model2' 		=> array('name' => 'model2', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Модель', 'maxlength' => 30),
						'god-vypuska' 	=> array('name' => 'god-vypuska', 'required' => TRUE, 'type' => 'integer', 	'translate' => 'Год выпуска', 'maxlength' => 5),	
						'probeg' 		=> array('name' => 'probeg', 'required' => TRUE, 'type' => 'integer', 	'translate' => 'Пробег', 'maxlength' => 8),	
						'tip-kuzova' 	=> array('name' => 'tip-kuzova', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Тип кузова', 'maxlength' => 30),
						'obem-dvigatelya' => array('name' => 'obem-dvigatelya', 'required' => TRUE, 'type' => 'numeric', 	'translate' => 'Объем двигателя', 'maxlength' => 5),	
						'privod' 		=> array('name' => 'privod', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Привод', 'maxlength' => 30),
						'steering-wheel' => array('name' => 'steering-wheel', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Руль', 'maxlength' => 30),
						'transmissiya' 	=> array('name' => 'transmissiya', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Трансмиссия', 'maxlength' => 30),
						'vid-topliva' 	=> array('name' => 'vid-topliva', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Вид топлива', 'maxlength' => 30),
						'tsvet' 		=> array('name' => 'tsvet', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Цвет', 'maxlength' => 30),						
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					)
	),
	'flat_resale'		=> array( 
			'id'    => 3,
			'name'	=> 'Продажа квартир и комнат (вторичное жилье)',
			'category' =>'flat_resale',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki5' 	=> array('name' => 'tip-sdelki5','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат', 'maxlength' => 30),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => TRUE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 3, //категория квартиры и комнаты
						'param_441' => 3196, //вторичное жилье
					),
			'filter'  =>array(
						'tip-sdelki5' => 3250,
						'build-type'  => 3196,
					)
	),
	'flat_rooms'	=> array( 
			'id'    => 3,
			'name'	=> 'Продажа комнат',
			'category' =>'flat_rooms',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki5' 	=> array('name' => 'tip-sdelki5','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => TRUE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 3, //категория квартиры и комнаты
						'param_441' => 3196, //вторичное жилье
						'param_440' => 3194, //комната
					),
			'filter'  =>array(
						'tip-sdelki5' => 3250,
						'build-type'  => 3196,
						'flatrooms'   => 3194
					)
	),
	'flat_new'		=> array( 
			'id'    => 3,
			'name'	=> 'Продажа квартир и комнат (новостройки)',
			'category' =>'flat_new',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki5' 	=> array('name' => 'tip-sdelki5','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат', 'maxlength' => 30),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => TRUE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'project_declare' => array('name' => 'project_declare',	'required' => FALSE, 'type' => 'text', 	'translate' => 'Декларация', 'maxlength' => 255),	
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 3, //категория квартиры и комнаты
						'param_441' => 3631, //новостройка, дом сдан
					),
			'filter'  =>array(
						'tip-sdelki5' => 3250,
						'build-type'  => array(3197,3631)
					)
	),
	'flat_rent'		=> array( 
			'id'    => 96,
			'name'	=> 'Аренда квартир и комнат',
			'category' =>'flat_rent',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki1' 	=> array('name' => 'tip-sdelki1','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат', 'maxlength' => 30),
						'nedv-type-pay' => array('name' => 'nedv-type-pay',	'required' => TRUE, 'type' => 'dict', 	'translate' => 'Срок аренды', 'maxlength' => 30),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => TRUE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 96, //категория аренда квартир и комнат
					),
			'filter'  =>array(
						'tip-sdelki1' => 3240,
					)
	),
	'flat_rooms_rent'		=> array( 
			'id'    => 96,
			'name'	=> 'Аренда квартир и комнат',
			'category' =>'flat_rooms_rent',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki1' 	=> array('name' => 'tip-sdelki1','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'flatrooms' 	=> array('name' => 'flatrooms',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Количество комнат', 'maxlength' => 30),
						'nedv-type-pay' => array('name' => 'nedv-type-pay',	'required' => TRUE, 'type' => 'dict', 	'translate' => 'Срок аренды', 'maxlength' => 30),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	
						'etazh' 		=> array('name' => 'etazh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Этаж', 'maxlength' => 2),	
						'etazhnost' 	=> array('name' => 'etazhnost',	'required' => TRUE, 'type' =>'integer', 	'translate' => 'Этажность', 'maxlength' => 2),			
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 96, //категория аренда квартир и комнат
					),
			'filter'  =>array(
						'tip-sdelki1' => 3240,
					)
	),
	'house'		=> array( 
			'id'    => 30,
			'name'	=> 'Дома, дачи, коттеджи',
			'category' =>'house',
			'fields'=>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'mestnost-raion-adres'		=> array('name' => 'mestnost-raion-adres',	'required' => FALSE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'dacha-chastnyi-dom' 	=> array('name' => 'dacha-chastnyi-dom','required' => TRUE, 'type' => 'dict', 'translate' => 'Вид объекта', 'maxlength' => 30),
						'ploshchad-doma' 		=> array('name' => 'ploshchad-doma',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь дома', 'maxlength' => 3),	
						'ploshchad-uchastka-v-sotkakh' 		=> array('name' => 'ploshchad-uchastka-v-sotkakh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь участка', 'maxlength' => 5),	
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 30,
					),
			'filter'  =>array(
						'tip-sdelki3' => 3242,
					)				
	),
	'land'		=> array( 
			'id'    => 34,
			'name'	=> 'Земельные участки',
			'category' =>'land',
			'fields'=>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => FALSE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'kategoriya-zemli' 	=> array('name' => 'kategoriya-zemli','required' => TRUE, 'type' => 'dict', 'translate' => 'Категория земли', 'maxlength' => 30),
						'ploshchad-uchastka-v-sotkakh' 		=> array('name' => 'ploshchad-uchastka-v-sotkakh',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь участка', 'maxlength' => 5),	
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 34,
					),
			'filter'  =>array(
						'tip-sdelki3' => 3242,
					)					
	),
	'commercial'		=> array( 
			'id'    => 4,
			'name'	=> 'Коммерческая недвижимость',
			'category' =>'commercial',
			'fields'=>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 50),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => FALSE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 50), 
						'object-tip' 	=> array('name' => 'object-tip','required' => TRUE, 'type' => 'dict', 'translate' => 'Тип объекта', 'maxlength' => 100),
						'ploshchad' 	=> array('name' => 'ploshchad',	'required' => TRUE, 	'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 5),	
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
						'rubricid' => 4,
					),
			'filter'  =>array(
						'tip-sdelki3' => 3242,
					)					
	),
	'auto_parts'	=> array( 
			'id'    => 23,
			'name'	=> 'Автозапчасти',
			'category' =>'auto_parts',
			'filter'=>array(),
			'autofill'=>array(
						'rubricid' => 23, //категория запчасти и принадлежности
						'param_473' => 3238, //продажа
					),
			'fields'  =>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'zapchasti-tip' => array('name' => 'zapchasti-tip', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Тип автозапчасти', 'maxlength' => 30),
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'title_adv' 	=> array('name' => 'title_adv','required' => TRUE,'type' => 'titleadv', 'translate' => 'Заголовок', 'maxlength' => 250),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					)
	),
	'vakansy'	=> array( 
			'id'    => 36,
			'name'	=> 'Вакансии',
			'category' =>'vakansy',
			'filter'=>array(),
			'autofill'=>array(
						'rubricid' => 36, //категория запчасти и принадлежности
					),
			'fields'  =>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'kompaniya' 	=> array('name' => 'kompaniya', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Компания', 'maxlength' => 250),
						'tip-vakansii' => array('name' => 'tip-vakansii', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Тип компании', 'maxlength' => 30),
						'sfera-deyatelnosti' => array('name' => 'sfera-deyatelnosti', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Сфера деятельности', 'maxlength' => 30),
						'tip-raboty' => array('name' => 'tip-raboty', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Тип работы', 'maxlength' => 30),
						'grafik-raboty' => array('name' => 'grafik-raboty', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'График работы', 'maxlength' => 30),
						'forma-oplaty' => array('name' => 'forma-oplaty', 'required' => FALSE, 'type' => 'dict', 	'translate' => 'Форма оплаты', 'maxlength' => 30),
						'professiya-dolzhnost' => array('name' => 'professiya-dolzhnost', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Должность', 'maxlength' => 250),
						'obyazannosti' => array('name' => 'obyazannosti', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Обязанности', 'maxlength' => 500),
						'usloviya-raboty' => array('name' => 'usloviya-raboty', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Условия', 'maxlength' => 500),
						'trebovaniya-k-kandidatu' => array('name' => 'trebovaniya-k-kandidatu', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Требования', 'maxlength' => 500),
						'zarplata' 		=> array('name' => 'zarplata',		'required' => FALSE, 'type' => 'integer', 	'translate' => 'Зарплата', 'maxlength' => 9),
						'adres-raion' => array('name' => 'adres-raion', 'required' => FALSE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 250),
						'user_text_adv' => array('name' => 'user_text_adv','required' => FALSE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					)
	),
	'garazhi_i_mashinomesta'		=> array( 
			'id'    => 25,
			'name'	=> 'Гаражи и машиноместа',
			'filter'=>array(),
			'category' =>'garazhi_i_mashinomesta',
			'fields'=>array
					(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'adres-raion'		=> array('name' => 'adres-raion',	'required' => TRUE, 'type' => 'text', 	'translate' => 'Адрес', 'maxlength' => 50),

						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30),

						'garazh-mashinomesto' 	=> array('name' => 'garazh-mashinomesto',	'required' => TRUE, 'type' => 'dict', 		'translate' => 'Гараж/машиноместо', 'maxlength' => 30),

						'tip-garazha' 	=> array('name' => 'tip-garazha',	'required' => FALSE, 'type' => 'dict', 		'translate' => 'Тип гаража', 'maxlength' => 30),

						'ploshchad-2x2' 	=> array('name' => 'ploshchad-2x2',	'required' => TRUE, 'type' => 'integer', 	'translate' => 'Площадь', 'maxlength' => 6),	

						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
						'contact'		=> array('name' => 'contact',	'required' => TRUE, 'type' => 'contact_name', 	'translate' => 'ФИО', 'maxlength' => 50),
						'images' 		=> array('name' => 'images',		'required' => FALSE,'type' => 'photo', 		'translate' => 'Фото', 'maxlength' => 0)
					),
			'autofill'=>array(
				'rubricid' => 25, //категория квартиры и комнаты
			)
	),

);