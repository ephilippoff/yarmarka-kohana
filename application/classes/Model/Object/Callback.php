<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Callback extends ORM
{
	protected $_table_name = 'object_callback';

	public function rules()
	{
		return array(
			'reason' => array(array('not_empty'),),
			'object_id' => array(array('not_empty'),),
		);
	}

}