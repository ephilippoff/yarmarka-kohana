<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact_Type extends ORM {

	const EMAIL		= 5;
	const ICQ		= 4;
	const SKYPE		= 3;
	const PHONE		= 2;
	const MOBILE	= 1;

	protected $_table_name = 'contact_type';

	protected $_has_many = array(
		'contacts' => array('model' => 'Object_Contact', 'foreign_key' => 'contact_type_id'),
	);
} // End Contact_Type Model
