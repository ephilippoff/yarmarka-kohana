<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Contact extends ORM
{
	protected $_table_name = 'object_contacts';

	protected $_belongs_to = array(
		'contact_obj'	=> array('model' => 'Contact', 'foreign_key' => 'contact_id'),
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

}

/* End of file Union.php */
/* Location: ./application/classes/Model/Object/Union.php */