<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Settings extends ORM {

	protected $_table_name = 'user_settings';

	protected $_belongs_to = array(
		'user'	=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

	public function rules()
	{
		return array(
			'user_id' => array(
				array('not_empty'),
			),
			'name' => array(
				array('not_empty'),
			),
			'value' => array(
				array('not_empty'),
			),
		);
	}
	public function sget($user_id)
	{
		$return = Array();
		$s = $this->where('user_id', '=', $user_id)->find_all();
		foreach ($s as $item)
			$return[$item->name] = $item->value;

		return $return;			
	}

	public function get_by_name($user_id, $name)
	{
		return $this->where('user_id', '=', $user_id)
					->where('name', '=', $name);			
	}

} // End User_Settings Model
