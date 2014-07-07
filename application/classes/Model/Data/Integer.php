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

	public function by_value_and_attribute($value, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'data_integer.attribute')
					->where("data_integer.value_min","=",$value)
					//->where("data_integer.value_max","=",$value)
					->where("attribute.seo_name","=",$seo_name)
					->find();
	}

	public function get_min_max_price($object_id)
	{
		$query = DB::select(DB::expr("MIN(value_min) as min, MAX(value_min) AS max"))
					->from($this->_table_name)
					->where("object", "=", $object_id)
					->where("attribute", "=", 44);
		$result = $query->execute();

		return Array (  (int) $result->get('min'),  (int) $result->get('max') );
	}
}

/* End of file Integer.php */
/* Location: ./application/classes/Model/Data/Integer.php */