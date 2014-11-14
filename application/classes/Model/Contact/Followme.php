<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact_Followme extends ORM {

	protected $_table_name = 'contact_followme';

	protected $_belongs_to = array(
		'contact'			=> array('model' => 'Contact', 'foreign_key' => 'contact_id')
	);
}