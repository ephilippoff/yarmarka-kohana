<?php defined('SYSPATH') or die('No direct script access.');


class Task_Vakancy extends Minion_Task
{
	protected $_options = array(
		"number" => NULL,
		"code" => NULL
	);

	protected function _execute(array $params)
	{
		$vakancies = ORM::factory('Object_Compiled')
						->join("object")
							->on("object_id","=","object.id")
						->where("category","=",36)
						->where("active","=",1)
						->where("is_published","=",1)
						->order_by("id","desc")
						->limit(5)
						->find_all();

		foreach ($vakancies as $vakancy) {
			Minion_CLI::write(Minion_CLI::color("id ", 'cyan').$vakancy->compiled);
		}
	
		

	}

}
