<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Text extends ORM
{
	protected $_table_name = 'data_text';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);
}

/* End of file Text.php */
/* Location: ./application/classes/Model/Data/Text.php */