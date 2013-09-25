<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Numeric extends Data
{
	protected $_table_name = 'data_numeric';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function filters()
	{
		return array(
			'value_min' => array(
				array('floatval'),
			),
			'value_max' => array(
				array('floatval'),
			),
		);
	}
}

/* End of file Numeric.php */
/* Location: ./application/classes/Model/Data/Numeric.php */