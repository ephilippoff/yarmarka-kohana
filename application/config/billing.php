<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'premium_enabled'		=> FALSE,
	'premium_ads_categories'=> array(),
	'premium_ads_price'		=> array(
					3 	=> 3,
					96 	=> 3,
					36 	=> 2
				),
	//ad_to_paper запрет размещения в рубрики по городам
	'ad_to_paper_locked_rubrics' => array(1979 => array(61, 65, 66, 67, 68, 69, 71, 72, 73, 74, 75), //Сургут
										  1919 => array(66)), //Тюмень
	'kupon' => array(
		'delivery_type' => 'electronic'
	),
	'emails_for_notify' => array("a.vagapov@yarmarka.biz")

);
