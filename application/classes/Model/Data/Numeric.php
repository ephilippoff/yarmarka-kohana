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

	public function get_compile()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->select_array(array("id","title","seo_name","type"));
		$result["_type"] = "Numeric";

		return $result;
	}

	public function by_object_and_attribute($object_id, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'data_numeric.attribute')
					->where("data_numeric.object","=",$object_id)
					->where("attribute.seo_name","IN", is_array($seo_name) ? $seo_name : array($seo_name))
					->find();
	}
}

/* End of file Numeric.php */
/* Location: ./application/classes/Model/Data/Numeric.php */