<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Module extends Model_Auth_User {

	protected $_table_name = 'module';

	protected $_has_many = array(
		//'user_roles' => array('model' => 'Role', 'through' => 'role_module', 'foreign_key' => 'module', 'far_key' => 'role'),
	);

} // End Module Model
