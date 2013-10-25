<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Contact extends ORM {

	protected $_belongs_to = array(
		'contact' 	=> array('model' => 'Contact', 'foreign_key' => 'contact_id'),
	);
}

/* End of file Contact.php */
/* Location: ./application/classes/Model/User/Contact.php */