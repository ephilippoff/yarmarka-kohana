<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Contacts extends ORM {
	protected $_table_name = 'object_contacts';

	protected $_belongs_to = array(
		'contact' => array('model' => 'Contact', 'foreign_key' => 'contact_id'),
	);
}

/* End of file Contacts.php */
/* Location: ./application/classes/Model/Object/Contacts.php */