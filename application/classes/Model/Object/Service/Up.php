<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Service_Up extends ORM
{
	protected $_table_name = 'object_service_up';

	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

}