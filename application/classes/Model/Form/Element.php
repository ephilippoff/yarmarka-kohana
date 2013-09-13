<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Form_Element extends ORM
{
	protected $_table_name = 'form_element';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'reference_obj'	=> array('model' => 'Reference', 'foreign_key' => 'reference'),
	);
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Form/Element.php */