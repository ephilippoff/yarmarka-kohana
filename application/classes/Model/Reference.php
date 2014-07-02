<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Reference extends ORM {

	protected $_table_name = 'reference';

	protected $_has_many = array(
		'form_elements' => array('model' => 'Form_Element', 'foreign_key' => 'reference'),
	);

	protected $_belongs_to = array(
		'category_obj' 	=> array('model' => 'Category', 'foreign_key' => 'category'),
		'attribute_obj'	=> array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function by_category_and_attribute($category_id, $seo_name)
	{
		return $this->join('attribute', 'left')
					->on('reference.attribute', '=', 'attribute.id')
					->where("category","=",$category_id)
					->where("attribute.seo_name","=",$seo_name)
					->find();
	}
}

/* End of file Reference.php */
/* Location: ./application/classes/Model/Reference.php */