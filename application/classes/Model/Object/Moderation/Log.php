<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Moderation_Log extends ORM {
	protected $_table_name = 'object_moderation_log';
	
	public function with_moderator()
	{
		return $this->select(array('user.email', 'user_email'))
				->select(array('user.fullname', 'user_fullname'))
			->join('user', 'left')
			->on('object_moderation_log.action_by', '=', 'user.id');
	}	
}

/* End of file Log.php */
/* Location: ./application/classes/Model/Object/Moderation/Log.php */