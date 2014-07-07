<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Union extends ORM
{
	protected $_table_name = 'object_union';

	protected $_belongs_to = array(
		'object' => array(),
	);

}

/* End of file Union.php */
/* Location: ./application/classes/Model/Object/Union.php */