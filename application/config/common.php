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
	'check_object_similarity' 				=> FALSE, //вкл выкл функционала проверки объявлений на похожесть (среди своих объяв)
	'max_object_similarity'					=> 0.97,	//процент похожести объявлений при котором объявления будут сгруппированы
	'union_objects_by_similarity' 			=> FALSE, //вкл выкл функционала группировки объявлений (зависит от check_object_similarity, если там false, то и тут false)
	'union_objects_by_similarity_by_cat' 	=> array(3,96), //ID рубрик в которых работает функционал группировки объявлений
	'address_attribute_ids'					=> array(47, 99),
	'time_to_running_line'					=> 7,
	'add_problem'							=> NULL,//'Уважаемые посетители сайта! Доводим до Вашего сведения, что законодательно изменились правила рассылки коммерческих СМС-сообщений. В связи с этим у абонентов "Мегафон" могут возникнуть проблемы с получением кода подтверждения на телефон при размещении объявления. Мы стараемся как можно быстрее устранить проблему. Благодарим за понимание!'
	'add_phone_required' 					=> TRUE,
	'add_phone_required_exlusion' 			=> array(36,35),
	'sphinx_prefix'							=> '_prod',
	'short_domain'							=> 'ya24.biz',
);