<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Plan extends ORM {

	protected $_table_name = 'plan';

	function get_next_plan($name, $number = 0)
	{
		return $this->where("name","=",$name)
					->where("number",">",$number)
					->order_by("number")
					->limit(1);
	}	

	function get_default_plan($plan_name)
	{
		return $this->where("name","=",$plan_name)
					->where("number","=",0);
	}

} // End User_Plan Model
