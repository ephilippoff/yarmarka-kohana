<?php defined('SYSPATH') OR die('No direct script access.');

class Plan {
	
	/**
	 * Проверка лимита на количество объявлений согласно тарифного плана
	 * 
	 * @param  integer 	$user_id
	 * @param  string 	$plan_name
	 * @param  integer 	$count
	 * @return int  текущий план
	 */
	public static function check_plan_limit_for_user($user_id, $plan_name = NULL, $count = 0)
	{
		$return = NULL;
		$user_plan = ORM::factory('User_Plan')->select("plan.title", "plan.count", array("plan.id","plan_id"))
							->join("plan")
							->on("plan.id","=","user_plan.plan_id")
							->where("plan.name","=",$plan_name)
							->where("user_id","=",$user_id)
							->where("date_expiration",">=",DB::expr('NOW()'))
							->find();
		if ($user_plan->loaded())
		{
			if ($count >= $user_plan->count)
				$return = $user_plan;
		} else {

			$user_plan = ORM::factory('Plan')
							->where("name","=",$plan_name)
							->where("number","=",0)
							->find();
			if ($user_plan->loaded())
			{
				if ($count >= $user_plan->count)
					$return = $user_plan;
			}
		}

		return $return;
	}

	/**
	 * Проверка лимита на количество объявлений согласно тарифного плана
	 * 
	 * @param  integer 	$user_id
	 * @param  integer 	$category_id
	 * @param  integer 	$add_to_count  1/0 учитывать ли добавившееся объвление, или только текущие
	 * @return array
	 */
	public static function check_plan_limit_for_user_and_category($user_id, $category_id, $add_to_count = 1)
	{
		$category = ORM::factory("Category", $category_id);
		
		$count = (int) ORM::factory("Object")
							->where("author","=",$user_id)
							->where("category","=",$category->id)
							->where("is_published","=",1)
							->where("active","=",1)
							->count_all();

		return Array( self::check_plan_limit_for_user($user_id, $category->plan_name, $count + $add_to_count), 
						$count);
	}


	public static function get_plan_error_description($plan_id)
	{
		$return = "";
		$plan = ORM::factory("Plan", $plan_id);
		$return .= "Ваш текущий тарифный план: '".$plan->title."'' с ограничением в ".$plan->count." объявлений. ";

		$plan_next = ORM::factory("Plan")
							->where("name","=",$plan->name)
							->where("number",">",$plan->number)
							->order_by("number")
							->limit(1)
							->find();
		if ($plan_next->loaded())
			$return .= "Вы можете приобрести следующий тарифный план : '".$plan_next->title."'' с ограничением в ".$plan_next->count." объявлений. ";
		else 
			$return .= "Это максимально возможный тарифный план";

		return $return;
	}

}

/* End of file Plan.php */
/* Location: ./application/classes/Plan.php */