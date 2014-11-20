<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'auto'		=> array( 
			'id'    => 15,
			'name'	=> 'Легковые автомобили',
			'category' =>'auto',
			'filter'=>array(),
			'autofill'=>array(
						'rubricid' => 15, //категория легковые автомобили
						'param_466' => 3238, //вторичное жилье
					),
			'fields'  =>array(
						'external_id' 	=> array('name' => 'external_id','required' => TRUE, 'type' => 'external_id', 'translate' => 'ID', 'maxlength' => 50),
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),

						'marka6' 		=> array('name' => 'marka6', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Марка', 'maxlength' => 30),
						'model2' 		=> array('name' => 'model2', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Модель', 'maxlength' => 30),
						'god-vypuska' 	=> array('name' => 'god-vypuska', 'required' => TRUE, 'type' => 'integer', 	'translate' => 'Год выпуска', 'maxlength' => 5),	
						'probeg' 		=> array('name' => 'probeg', 'required' => TRUE, 'type' => 'integer', 	'translate' => 'Пробег', 'maxlength' => 8),	
						'tip-kuzova' 	=> array('name' => 'tip-kuzova', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Тип кузова', 'maxlength' => 30),
						'obem-dvigatelya' => array('name' => 'obem-dvigatelya', 'required' => TRUE, 'type' => 'integer', 	'translate' => 'Объем двигателя', 'maxlength' => 5),	
						'privod' 		=> array('name' => 'privod', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Привод', 'maxlength' => 30),
						'steering-wheel' => array('name' => 'steering-wheel', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Руль', 'maxlength' => 30),
						'transmissiya' 	=> array('name' => 'transmissiya', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Трансмиссия', 'maxlength' => 30),
						'vid-topliva' 	=> array('name' => 'vid-topliva', 'required' => TRUE, 'type' => 'dict', 	'translate' => 'Вид топлива', 'maxlength' => 30),
						'tsvet' 		=> array('name' => 'tsvet', 'required' => TRUE, 'type' => 'text', 	'translate' => 'Цвет', 'maxlength' => 30),
						
						'tsena' 		=> array('name' => 'tsena',		'required' => TRUE, 'type' => 'integer', 	'translate' => 'Цена', 'maxlength' => 9),
						'user_text_adv' => array('name' => 'user_text_adv','required' => TRUE,'type' => 'textadv', 'translate' => 'Текст объявления', 'maxlength' => 15000),
						'contact_0_value'=> array('name' => 'contact_0_value',	'required' => TRUE, 'type' => 'contact','translate' => 'Телефон №1', 'maxlength' => 40),
						'contact_1_value'=> array('name' => 'contact_1_value',	'required' => FALSE, 'type' => 'contact','translate' => 'Телефон №2', 'maxlength' => 40),
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
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'address'		=> array('name' => 'address',	'required' => TRUE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'address'		=> array('name' => 'address',	'required' => FALSE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'address'		=> array('name' => 'address',	'required' => FALSE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
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
						'city' 			=> array('name' => 'city',		'required' => TRUE, 'type' => 'city', 		'translate' => 'Город', 'maxlength' => 30),
						'address'		=> array('name' => 'address',	'required' => FALSE, 'type' => 'address', 	'translate' => 'Адрес', 'maxlength' => 50),
						'tip-sdelki3' 	=> array('name' => 'tip-sdelki3','required' => TRUE,'type' => 'dict', 		'translate' => 'Тип сделки', 'maxlength' => 30), 
						'object-tip' 	=> array('name' => 'object-tip','required' => TRUE, 'type' => 'dict', 'translate' => 'Тип объекта', 'maxlength' => 30),
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


);