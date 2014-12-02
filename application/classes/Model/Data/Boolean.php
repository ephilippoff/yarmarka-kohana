<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Boolean extends Data
{
	protected $_table_name = 'data_boolean';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function get_compile()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->as_array("id","title");

		return $result;
	}
}

/* End of file Boolean.php */
/* Location: ./application/classes/Model/Data/Boolean.php */