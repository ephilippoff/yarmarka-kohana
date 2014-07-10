<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Settings extends ORM {

	protected $_table_name = 'user_settings';

	public function get($user_id)
	{
		$return = Array();
		$s = $this->where('user_id', '=', $user_id)->find_all();
		foreach ($s as $item)
			$return[$item->name] = $item->value;

		return $return;			
	}

	public function get_by_name($user_id, $name)
	{
		$s = $this->where('user_id', '=', $user_id)
					->where('name', '=', $name)->find();
		return $s->value;			
	}

} // End User_Settings Model
