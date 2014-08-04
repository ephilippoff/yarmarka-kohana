<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Massload extends ORM
{
	protected $_table_name = 'object_massload';

	protected $_belongs_to = array(
		'massload_obj'	=> array('model' => 'Massload', 'foreign_key' => 'massload_id'),
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

}

