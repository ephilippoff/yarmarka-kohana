<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Movement extends ORM
{
	protected $_table_name = 'object_movement';

	protected $_belongs_to = array(
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
		'kupon_obj'	=> array('model' => 'Kupon', 'foreign_key' => 'kupon_id'),
	);

}

