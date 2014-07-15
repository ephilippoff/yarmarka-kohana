<?php defined('SYSPATH') OR die('No direct script access.');

class Plan {
	
	/**
	 * Проверка лимита на количество объявлений согласно тарифного плана
	 * 
	 * @param  integer 	$user_id
	 * @param  string 	$plan_name
	 * @return obj  текущий план
	 */
	public static function get_plan_for_user($user_id, $plan_name = NULL)
	{
		$user_plan = ORM::factory('User_Plan')->get_plan($user_id, $plan_name)->find();
		if (!$user_plan->loaded())
		{			
			$user_plan = ORM::factory('Plan')->get_default_plan($plan_name)->find();
		}
		return $user_plan;
	}

	public static function check_count($user_plan, $count = 0)
	{
		if ($user_plan->loaded())
			return ($count >= $user_plan->count) ? FALSE : TRUE;
		else 
			return FALSE;
	}

	/**
	 * Проверка лимита на количество объявлений согласно тарифного плана
	 * 
	 * @param  integer 	$user_id
	 * @param  integer 	$category_id
	 * @return obj
	 */
	public static function get_plan_for_user_by_category($user_id, $category_id)
	{		
		return self::get_plan_for_user($user_id, ORM::factory("Category", $category_id)->plan_name);
	}


	public static function get_plan_error_description($plan_id)
	{
		$return = "";
		$plan = ORM::factory("Plan", $plan_id);
		$return .= "Ваш текущий тарифный план: '".$plan->title."'' с ограничением в ".$plan->count." объявлений. ";

		$plan_next = ORM::factory("Plan")->get_next_plan($plan->name, $plan->number)->find();

		if ($plan_next->loaded())
			$return .= "Вы можете приобрести следующий тарифный план : '".$plan_next->title."'' с ограничением в ".$plan_next->count." объявлений. ";
		else 
			$return .= "Это максимально возможный тарифный план";

		return $return;
	}

}

/* End of file Plan.php */
/* Location: ./application/classes/Plan.php */