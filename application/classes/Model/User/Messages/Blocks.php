<?php defined('SYSPATH') OR die('No direct script access.');

class Model_User_Messages_Blocks extends ORM
{
	protected $_table_name = 'user_messages_blocks';

	protected $_belongs_to = array(
		'object' 	=> array(),
		'user' 		=> array(),
	);
}

/* End of file Blocks.php */
/* Location: ./application/classes/Model/User/Messages/Blocks.php */