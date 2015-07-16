<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Date extends Data
{
	protected $_table_name = 'data_date';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function get_compile()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->select_array(array("id","title"));
		$result["_type"] = "Date";

		return $result;
	}
}

/* End of file Date.php */
/* Location: ./application/classes/Model/Data/Date.php */