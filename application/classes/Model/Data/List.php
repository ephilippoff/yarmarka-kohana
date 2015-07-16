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

		$result["_attribute"] 	= $this->attribute_obj->select_array(array("id","title","seo_name","type"));
		$result["_element"] 	= $this->attribute_element_obj->select_array(array("id","title","seo_name"));
		$result["_type"] = "List";

		return $result;
	}

	public function by_object_and_attribute($object_id, $seo_name)
	{
		return $this->select(array('*', 'attribute_element.seo_name'))
					->join('attribute')
					->on('attribute.id', '=', 'data_list.attribute')
					->join('attribute_element')
						->on('attribute_element.id', '=', 'data_list.value')
					->where("data_list.object","=",$object_id)
					->where("attribute.seo_name","=",$seo_name)
					->find();
	}
}

/* End of file List.php */
/* Location: ./application/classes/Model/Data/List.php */