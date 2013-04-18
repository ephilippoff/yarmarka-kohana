<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Role extends Model_Auth_Role {
	protected $_table_name = 'role';

	// Relationships
	protected $_has_many = array(
		'users'   => array('model' => 'User'),
		'modules' => array('model' => 'Module', 'through' => 'role_module', 'foreign_key' => 'role', 'far_key' => 'module'),
	);

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
				array('max_length', array(':value', 32)),
			),
			'description' => array(
				array('max_length', array(':value', 255)),
			)
		);
	}
} // End Role Model
