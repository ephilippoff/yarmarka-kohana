<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Link_Request extends ORM {

	protected $_table_name = 'user_link_requests';

	protected $_belongs_to = array(
		'user' 			=> array('model' => 'User', 'foreign_key' => 'user_id'),
		'linked_user'	=> array('model' => 'User', 'foreign_key' => 'linked_user_id'),
	);

	public function delete_request($user_id, $linked_user_id)
	{
		return $this->decline_request($user_id, $linked_user_id);
	}

	public function decline_request($user_id, $linked_user_id)
	{
		return $this->where("user_id","=",$user_id)
					->where("linked_user_id","=",$linked_user_id)
					->delete_all();
	}

	public function delete_requests($linked_user_id)
	{
		return $this->where("linked_user_id","=",$linked_user_id)
					->delete_all();
	}

}

/* End of file Link.php */
/* Location: ./application/classes/Model/User/Link.php */