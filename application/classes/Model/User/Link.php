<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Link extends ORM {

	protected $_table_name = 'user_links';

	protected $_belongs_to = array(
		'user' 			=> array(),
		'linked_user'	=> array('model' => 'User', 'foreign_key' => 'linked_user_id'),
	);
}

/* End of file Link.php */
/* Location: ./application/classes/Model/User/Link.php */