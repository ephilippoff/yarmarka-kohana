<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Plan extends ORM {

	protected $_table_name = 'user_plan';

	public function by_user($user_id)
	{
		return $this->where("user_id","=",$user_id)->find_all();
	}

} // End User_Plan Model
