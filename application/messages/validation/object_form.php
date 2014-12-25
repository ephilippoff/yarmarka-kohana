<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'not_empty' 		=> 'Поле `:param2` обязательно для заполнения',
	'not_empty_html' 	=> 'Поле `:param2` обязательно для заполнения',	
	'not_0' 			=> 'Значение поля `:param2` должно быть больше нуля',
	'empty_contacts' 	=> 'Необходимо добавить хотя бы один верифицированный контакт для связи. Подтверждение мобильного телефона по смс, городского - звонком из нашего контакт центра.',
	'blocked_contacts' 	=> ':contacts в черном списке',
	'login_failed' 		=> 'Невереное сочетание логина и пароля',
	'max_objects'		=> 'Вы подали максимальное количество объявлений в эту рубрику. Смените тип учетной записи на "Компания" чтобы дать больше объявлений <a href="/user/userinfo">здесь</a>',
	'max_objects_company' => 'Модератором установлено индивидуальное ограничение на количество объявлений в эту рубрику, по причине нарушений правил размещения объявлений на сайте, либо изза рода деятельности',
	'min_length'		=> 'Поле `:param3` должно содержать не менее :param2 символов',
	'max_length'		=> 'Поле `:param3` должно содержать не более :param2 символов',
	'not_autorized'		=> 'Вы не авторизованы на сайте, введите логин и пароль',
	'not_empty_photo' 	=> 'Не загружен`:param2`',
	'inn' 				=> 'В поле `:param2` введен неправильный ИНН, возможно ошиблись в цифрах',
	'digit'				=> 'Поле `:param2` может содержать только цифры 0-9',
	'numeric'			=> 'Поле `:param2` может содержать только целые и дробные значения. Целая и дробная часть отделяется точкой. Например 12.9',
	'min_value'			=> 'Значение поля `:param2` не может быть менее :param3',
	'max_value'			=> 'Значение поля `:param2` не можеть быть более :param3'
);
