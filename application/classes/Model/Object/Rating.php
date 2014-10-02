<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Rating extends ORM
{
	protected $_table_name = 'object_rating';

	protected $_belongs_to = array(
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

}

