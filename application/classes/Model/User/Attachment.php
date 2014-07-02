<?php defined('SYSPATH') OR die('No direct script access.');

class Model_User_Attachment extends ORM
{
	protected $_table_name = 'user_attachment';
	
	protected $_belongs_to = array(
		'user' => array(),
	);

	
}

/* End of file Attachment.php */
/* Location: ./application/classes/Model/User/Attachment.php */