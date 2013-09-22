<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact_Type extends ORM {

	const EMAIL		= 5;
	const ICQ		= 4;
	const SKYPE		= 3;
	const PHONE		= 2;
	const MOBILE	= 1;

	protected $_table_name = 'contact_type';

	protected $_has_many = array(
		'contacts' => array('model' => 'Contact', 'foreign_key' => 'contact_type_id'),
	);

	public static function is_phone($contact_type_id)
	{
		return in_array(intval($contact_type_id), array(self::PHONE, self::MOBILE), TRUE);
	}
}

/* End of file Type.php */
/* Location: ./application/classes/Model/Contact/Type.php */