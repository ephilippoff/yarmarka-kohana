<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact_Type extends ORM {

	const EMAIL		= 5;
	const ICQ		= 4;
	const SKYPE		= 3;
	const PHONE		= 2;
	const MOBILE	= 1;
	const OTHER		= 0;

	protected static $types = array(
		0 => "other",
		1 => "mobile",
		2 => "phone",
		3 => "skype",
		4 => "icq",
		5 => "email",
	);

	protected $_table_name = 'contact_type';

	protected $_has_many = array(
		'contacts' => array('model' => 'Contact', 'foreign_key' => 'contact_type_id'),
	);

	public static function is_phone($contact_type_id)
	{
		return in_array(intval($contact_type_id), array(self::PHONE, self::MOBILE), TRUE);
	}

	// @todo проверяет только email, мобильный и домашний телефон
	public static function detect_contact_type($value)
	{
		if (!$value) return FALSE;
		$value = trim(strtolower($value));

		if (Valid::email($value))
			return self::EMAIL;

		if (strpos($value, '79') === 0)
			return self::MOBILE;

		return self::PHONE;
	}

	public static function detect_contact_type_massload($contact)
	{
		if (strpos($contact, '9') === 0)
			return self::MOBILE;
		elseif (gettype((int)substr($contact, 0,1)) == "integer" AND (int)substr($contact, 0,1) > 0)
			return self::PHONE;

		if (Valid::email($contact))
			return self::EMAIL;

		return self::OTHER;
	}

	public static function get_verifiyng_types()
	{
		return array(
			self::EMAIL, self::PHONE, self::MOBILE
		);
	}

	public static function get_type_name($type)
	{
		return self::$types[$type];
	}

	public static function get_type_id($name)
	{
		$types = array_flip(self::$types);

		return $types[$name];
	}
}

/* End of file Type.php */
/* Location: ./application/classes/Model/Contact/Type.php */