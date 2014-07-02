<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute_Element extends ORM
{
	protected $_table_name = 'attribute_element';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function by_value_and_attribute($value, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'attribute_element.attribute')
					->where("attribute_element.title","=",$value)
					->where("attribute.seo_name","=",$seo_name)
					->find();
	}
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Attribute/Element.php */