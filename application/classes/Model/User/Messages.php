<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Messages extends ORM {

	protected $_created_column  = array('column' => 'createdOn', 'format' => 'Y-m-d H:i:s');

	protected $_table_name = 'user_messages';

	protected $_has_many = array(
		'users'	=> array(),
	);

	protected $_belongs_to = array(
		'object'	=> array(),
		'user' => array(),
	);

	public function from_moderator()
	{
		return $this->join('user', 'left')
			->on('user_messages.user_id', '=', 'user.id')
			->where('user.role', 'IN', array(1,3))
			->where('user_messages.parent_id', 'is', DB::expr('NULL'));
	}

	public function add_msg_to_object($object_id, $text)
	{
		$this->object_id 	= intval($object_id);
		$this->text 		= trim($text);
		if ($user = Auth::instance()->get_user())
		{
			$this->user_name	= $user->fullname ? $user->fullname : $user->email;
			$this->user_id 		= $user->id;
			$this->email 		= $user->email;
			$this->number 		= $user->phone;
		}

		return $this->save();
	}

} // End User_Messages Model
