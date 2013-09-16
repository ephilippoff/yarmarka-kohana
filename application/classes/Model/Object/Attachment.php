<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Attachment extends ORM
{
	protected $_table_name = 'object_attachment';
	
	protected $_belongs_to = array(
		'object' => array(),
	);
}

/* End of file Attachment.php */
/* Location: ./application/classes/Model/Object/Attachment.php */