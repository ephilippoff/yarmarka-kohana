<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute_Action extends ORM
{
	protected $_table_name = 'attribute_action';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute_id'),
		'action_obj' => array('model' => 'Action', 'foreign_key' => 'action_id'),
		'attribute_element_obj' => array('model' => 'Attribute_Element', 'foreign_key' => 'value_id'),
	);
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Attribute/Element.php */