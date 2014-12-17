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
					->cached(Date::DAY)
					->find();
	}

	public function with_attribute_by_id($id)
	{
		return $this->select("attribute.title", "attribute.type", "attribute.solid_size", "attribute.frac_size", "attribute.max_text_length")
					->join("attribute","left")
						->on("reference.attribute","=","attribute.id")
					->where("reference.id","=", $id);
	}
}

/* End of file Reference.php */
/* Location: ./application/classes/Model/Reference.php */