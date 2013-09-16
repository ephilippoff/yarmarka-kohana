<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Date extends ORM
{
	protected $_table_name = 'data_date';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);
}

/* End of file Date.php */
/* Location: ./application/classes/Model/Data/Date.php */