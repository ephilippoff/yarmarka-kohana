<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_List extends Data
{
	protected $_table_name = 'data_list';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
		'attribute_element_obj' => array('model' => 'Attribute_Element', 'foreign_key' => 'value'),
	);

	public function get_compile()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->as_array("id","title");
		$result["_element"] 	= $this->attribute_element_obj->as_array("id","title");
		$result["_type"] = "List";

		return $result;
	}

	public function get_compile_surgut()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->as_array("id","title");
		$result["_element"] 	= $this->attribute_element_obj->as_array("id","title");
		$result["_type"] = "List";

		return $result;
	}
}

/* End of file List.php */
/* Location: ./application/classes/Model/Data/List.php */