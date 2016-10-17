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

	public function get_by_group_and_name($user_id, $type, $name)
	{
		return $this->where('user_id', '=', $user_id)
					->where("type","=", $type)
					->where('name', '=', $name);			
	}

	public function get_group($user_id, $type)
	{
		$result = array();

		$settings = $this->where("user_id","=",$user_id)
						 ->where("type","=", $type)
						 ->find_all();
		foreach ($settings as $setting) {
			$result[$setting->name] = $setting->value;
		}

		return $result;
	}

	public function update_or_save($user_id, $type, $name, $value)
	{

		$setting = ORM::factory('User_Settings')
										->where("user_id","=",$user_id)
										->where("name","=",$name)
										->where("type","=", $type)
										->find();
		
		$setting->created_on = DB::expr("NOW()");						
		$setting->user_id = $user_id;				
		$setting->type = $type;
		$setting->name = $name;
		$setting->value = $value;
		$setting->save();

		return $setting;
	}

	public function _delete($user_id, $type, $name)
	{
		return ORM::factory('User_Settings')
							->where("user_id","=",$user_id)
							->where("name","=",$name)
							->where("type","=",$type)
							->delete_all();
	}

	public function get_last_freeup_date($user_id)
	{
		return $this->get_by_name($user_id, 'freeup_date')->find();
	}

	public function freeup_exists($user_id)
	{
		$last = $this->get_by_name($user_id, 'freeup_date')->find();

		return ( !$last->loaded() OR ($last->loaded() AND strtotime($last->value) < strtotime( date('Y-m-d H:i:s', strtotime('-10 days'))) ) ) ? 1 : 0;
	}

	public function freeup_save($user_id, $type = 'freeup_date')
	{
		return $this->update_or_save($user_id, NULL, $type, date('Y-m-d H:i:s') );
	}

	public function freeup_remove($user_id, $type = 'freeup_date')
	{
		return ORM::factory('User_Settings')
							->where("user_id","=",$user_id)
							->where("name","=",$type)
							->delete_all();
	}

} // End User_Settings Model
