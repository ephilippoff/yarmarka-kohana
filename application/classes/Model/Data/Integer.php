<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Integer extends ORM
{
	protected $_table_name = 'data_integer';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);
}

/* End of file Integer.php */
/* Location: ./application/classes/Model/Data/Integer.php */