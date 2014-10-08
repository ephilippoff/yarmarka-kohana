<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(

	'ymaps_api_key'       => 'AFTgAVIBAAAAfkvxdAIAH91oyVwi9cgVvG7DwdokWntFwXUAAAAAAAAAAABVZ_MtL04fJbwMGBGwSCeiUCxXRQ==',
	'address_precision'	  => array(
			array(
				//вторичное жилье
				"category_id" => 3,
				"filters" => array("rubricid" => 3, "param_441" => array(3196)),
				"precision" => "exact",
				"error_text" => "Для вторичного жилья, адрес необходимо указать с точностью до дома. Например: ул. Ленина, д. 2"
			),
			array(
				//новостройки
				"category_id" => 3,
				"filters" => array("rubricid" => 3, "param_441" => array(3197,3631)),
				"precision" => "street",
				"error_text" => "Для новостроек, адрес можно указать с точностью до улицы. Например: ул. Ленина"
			),
			array(
				//аренда жалья
				"category_id" => 96,
				"filters" => array("rubricid" => 96),
				"precision" => "exact",
				"error_text" => "Адрес необходимо указать с точностью до дома. Например: ул. Ленина, д. 2"
			)
		)
);
