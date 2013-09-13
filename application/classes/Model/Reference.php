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
}

/* End of file Reference.php */
/* Location: ./application/classes/Model/Reference.php */