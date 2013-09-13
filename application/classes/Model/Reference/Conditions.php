<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Reference_Conditions extends ORM {

	protected $_table_name = 'reference_conditions';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'reference_obj' => array('model' => 'Reference', 'foreign_key' => 'reference'),
	);
}

/* End of file Conditions.php */
/* Location: ./application/classes/Model/Reference/Conditions.php */