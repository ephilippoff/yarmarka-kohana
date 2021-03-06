<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'main_domain'							=> 'yarmarka.biz',
	'main_category'							=> 'glavnaya-kategoriya',
	'is_local' 								=> 0,
	'default_region_id'						=> 73,
	'max_count_photo'						=> 10,
	'days_count_between_service_up' 		=> 3,
	'days_count_between_service_up_by_cat'  => array(36 => 1),
	'disallowed_email_domains' 				=> array('yopmail.com', 'asdasd.com'),
	'admin_emails' 							=> array('a.vagapov@yarmarka.biz'),
	'max_image_similarity' 					=> 0.97,
	'check_image_similarity' 				=> FALSE,
	'check_object_similarity' 				=> FALSE, //вкл выкл функционала проверки объявлений на похожесть (среди своих объяв)
	'max_object_similarity'					=> 0.97,	//процент похожести объявлений при котором объявления будут сгруппированы
	'address_attribute_ids'					=> array(47, 99),
	'time_to_running_line'					=> 7,
	'add_problem'							=> NULL,//'Уважаемые посетители сайта! Доводим до Вашего сведения, что законодательно изменились правила рассылки коммерческих СМС-сообщений. В связи с этим у абонентов "Мегафон" могут возникнуть проблемы с получением кода подтверждения на телефон при размещении объявления. Мы стараемся как можно быстрее устранить проблему. Благодарим за понимание!'
	'sphinx_prefix'							=> '_prod',
	'date_new_registration'					=> '2014-11-21',
	'add_phone_required' 					=> TRUE,
	'add_phone_required_exlusion' 			=> array(36,35),
	'short_domain'							=> 'ya24.biz',
	'sync_prefix'							=> '_prod',
	'banner_zone_positions'					=> array(3 => 18, 10 => 19, 15 => 40, 20 => 52),
	'site_disable' => TRUE,
	'white_ips' => array("195.68.130.122", "127.0.0.1", "146.185.226.9" , "217.66.156.163"),
	'vk_app_secret' => 'qwe',
	'send_complaints_to' => array( 'amezhenny72@gmail.com', 'e.philippoff@gmail.com', 'support@yarmarka.userecho.com' ),
	'main_cities' =>  array('','nizhnevartovsk','tyumen','surgut','ekaterinburg','nefteyugansk','khanty-mansiisk')
);
