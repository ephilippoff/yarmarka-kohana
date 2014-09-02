<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Up extends Minion_Task
{

	protected $_options = array(
		'up_percent'	=> FALSE
	);

	protected function _execute(array $params)
	{
		$up_percent = $params['up_percent'];
		if (!$up_percent)
			$up_percent = Kohana::$config->load('massload.up_percent');

		$auto_ups = ORM::factory('User_Settings')
					->where("name","=","auto_up")
					->find_all();
		foreach ($auto_ups as $setting) {
			$us = ORM::factory('User_Settings')
							->get_by_name($setting->user_id, "up_percent")
							->find();
			if ($us->loaded())
				$up_percent = $us->value;

			Minion_CLI::write('Username: '.$setting->user->org_name);
			Minion_CLI::write('Percent:  '.$up_percent);

			$count = ORM::factory('Object')
						->where("author","=",$setting->user_id)
						->where("is_published","=",1)
						->count_all();
			Minion_CLI::write('Count:  '.$count);
			$count_for_up = round($count * ($up_percent/100));
			Minion_CLI::write('Count for up:  '.$count_for_up);

			$sub = DB::select('id')->from('object')
						->where("author","=",$setting->user_id)
						->where("is_published","=",1)
						->order_by( DB::expr('RANDOM()') )
						->limit($count_for_up);

			ORM::factory('Object')
				->where("id","IN", $sub)
				->set("date_created", DB::expr('NOW()'))
				->update_all();

		}

	}
}