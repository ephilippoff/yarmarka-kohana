<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Notice extends ORM
{

	const PHOTO = 'PHOTO';
	const UP = 'UP';
	const EXPIRATION = 'EXPIRATION';

	protected $_table_name = 'object_notice';

	protected $_belongs_to = array(
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

	

}

