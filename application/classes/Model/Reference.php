<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Reference extends ORM {

	protected $_table_name = 'reference';
	
	public function rules()
	{
		return array(
			'category' => array(array('digit'), array('not_empty'),),
			'attribute' => array(array('digit'), array('not_empty'),),
			'weight' => array(array('digit'),),
			'is_required' => array(array('digit'),),
			'is_title' => array(array('digit'),),
			'is_main' => array(array('digit'),),
			'attribute_cols_count' => array(array('digit'),),
			'is_seo_used' => array(array('digit'),),
			'is_selectable' => array(array('digit'),),		
		);
	}

	public function filters()
	{
		return array(
			'category' => array(array('intval'),),
			'attribute' => array(array('intval'),),
			'weight' => array(array('intval'),),
			'is_required' => array(array('intval'),),
			'is_title' => array(array('intval'),),
			'is_main' => array(array('intval'),),
			'attribute_cols_count' => array(array('intval'),),
			'is_seo_used' => array(array('intval'),),
			'is_selectable' => array(array('intval'),),
		);
	}		
	
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
	
	public function with_attributes()
	{
		return $this->select(array( "attribute.title",  "attribute_title"))
					->join("attribute", "left")
						->on("reference.attribute", "=", "attribute.id");
	}	
	
	public function with_categories()
	{
		return $this->select(array( "category.title",  "category_title"))
					->join("category", "left")
						->on("reference.category", "=", "category.id");
	}	
	
}

/* End of file Reference.php */
/* Location: ./application/classes/Model/Reference.php */