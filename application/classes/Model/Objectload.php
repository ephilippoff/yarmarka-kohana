<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Objectload extends ORM {

	protected $_table_name = 'objectload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);


	function update_statistic()
	{
		if (!$this->loaded())
			return;

		$common_statistic = array(
				"all" => 0,
				"loaded" => 0,
				"error"  => 0,
				"edited" => 0
			);

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id","=", $this->id)
				->where("table_name","IS NOT", NULL)
				->find_all();
		foreach ($of as $file)
		{
			$statistic = array();
			$common_statistic["all"]    += $statistic["all"]   = ORM_Temp::factory($file->table_name)
																	->count_all();

			$common_statistic["loaded"] += $statistic["loaded"] = ORM_Temp::factory($file->table_name)
																	->where("loaded","=",1)
																	->count_all();

			$common_statistic["error"] += $statistic["error"]  = ORM_Temp::factory($file->table_name)
																	->where("error","=",1)
																	->count_all();

			$common_statistic["edited"] += $statistic["edited"] = ORM_Temp::factory($file->table_name)
																	->where("edited","=",1)
																	->count_all();

			ORM::factory('Objectload_Files')
				->where("id","=",$file->id)
				->set("statistic",serialize($statistic))
				->update_all();
		}

		$this->statistic = serialize($common_statistic);
		$this->update();
	}
} 
