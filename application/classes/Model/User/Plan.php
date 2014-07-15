<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Plan extends ORM {

	protected $_table_name = 'user_plan';

	protected $_belongs_to = array(
		'user' 	=> array('model' => 'User', 'foreign_key' => 'user_id'),
		'plan' 	=> array('model' => 'Plan', 'foreign_key' => 'plan_id'),
	);

	public function by_user(int $user_id)
	{
		return $this->where("user_id","=",$user_id)->find_all();
	}

	public function get_plan($user_id, $plan_name)
	{
		return $this->select("plan.title", "plan.count", array("plan.id","plan_id"))
				->join("plan")
					->on("plan.id","=","user_plan.plan_id")
				->where("plan.name"	,"=",$plan_name)
				->where("user_id","=",$user_id)
				->more_or_equal_than_now("date_expiration");
	}

	public function get_plans($user_id)
	{
		return $this->select("plan.name")
					->join("plan")
						->on("plan.id","=","user_plan.plan_id")
					->where("user_id","=",$user_id)
					->more_or_equal_than_now("date_expiration");
	}

} // End User_Plan Model
