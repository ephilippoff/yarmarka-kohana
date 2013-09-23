<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Integer extends Data
{
	protected $_table_name = 'data_integer';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function filters()
	{
		return array(
			'value_min' => array(
				array('intval'),
			),
			'value_max' => array(
				array('intval'),
			),
		);
	}
}

/* End of file Integer.php */
/* Location: ./application/classes/Model/Data/Integer.php */