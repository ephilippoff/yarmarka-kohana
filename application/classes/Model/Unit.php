<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Unit extends ORM {
	protected $_table_name = 'unit';

	// Relationships
	protected $_has_many = array(
		'users'   => array(
                    'model' => 'User',
                    'through' => 'user_units'
                )
            
	);

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 250)),
			),
		);
	}
}
