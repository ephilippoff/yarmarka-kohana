<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'main_domain'							=> 'yarmarka.biz',
	'is_local' 								=> 0,
	'default_region_id'						=> 73,
	'max_count_photo'						=> 10,
	'days_count_between_service_up' 		=> 3,
	'days_count_between_service_up_by_cat'  => array(36 => 1),
	'disallowed_email_domains' 				=> array('yopmail.com', 'asdasd.com'),
	'admin_emails' 							=> array('a.vagapov@yarmarka.biz'),
	'max_image_similarity' 					=> 0.97,
	'check_image_similarity' 				=> FALSE,
	'check_object_similarity' 				=> TRUE, //вкл выкл функционала проверки объявлений на похожесть (среди своих объяв)
	'max_object_similarity'					=> 0.89,	//процент похожести объявлений при котором объявления будут сгруппированы
	'union_objects_by_similarity' 			=> TRUE, //вкл выкл функционала группировки объявлений (зависит от check_object_similarity, если там false, то и тут false)
	'union_objects_by_similarity_by_cat' 	=> array(3,96), //ID рубрик в которых работает функционал группировки объявлений
	'address_attribute_ids'					=> array(47, 99),
	'time_to_running_line'					=> 7
);