<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute_Relation extends ORM
{
	protected $_table_name = 'attribute_relation';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'attribute_element_obj' => array('model' => 'Attribute_Element', 'foreign_key' => 'parent_element_id'),
		'category_obj' => array('model' => 'Category', 'foreign_key' => 'category_id'),
		'parent_element_id' => array('model' => 'Attribute_Relation', 'foreign_key' => 'parent_id'),
		'reference_obj' => array('model' => 'Reference', 'foreign_key' => 'reference_id'),
		'parent_obj' => array('model' => 'Attribute_Relation', 'foreign_key' => 'parent_id'),
	);

	
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Attribute/Element.php */