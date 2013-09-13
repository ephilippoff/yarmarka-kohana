<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact_Block_List extends ORM {

	protected $_table_name = 'contact_block_list';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'contact_type' => array('model' => 'Contact_Type', 'foreign_key' => 'contact_type_id'),
	);
}

/* End of file List.php */
/* Location: ./application/classes/Model/Contact/Block/List.php */