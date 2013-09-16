<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Boolean extends ORM
{
	protected $_table_name = 'data_boolean';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);
}

/* End of file Boolean.php */
/* Location: ./application/classes/Model/Data/Boolean.php */