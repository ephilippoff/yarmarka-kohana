<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_List extends Data
{
	protected $_table_name = 'data_list';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
		'attribute_element_obj' => array('model' => 'Attribute_Element', 'foreign_key' => 'value'),
	);
}

/* End of file List.php */
/* Location: ./application/classes/Model/Data/List.php */