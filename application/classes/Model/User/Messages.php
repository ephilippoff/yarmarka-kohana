<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Messages extends ORM {

	protected $_table_name = 'user_messages';

	protected $_has_many = array(
		'users'	=> array(),
	);

	protected $_belongs_to = array(
		'object'	=> array(),
	);

	public function from_moderator()
	{
		return $this->join('user', 'left')
			->on('user_messages.user_id', '=', 'user.id')
			->where('user.role', 'IN', array(1,3))
			->where('user_messages.parent_id', 'is', DB::expr('NULL'));
	}

} // End User_Messages Model
