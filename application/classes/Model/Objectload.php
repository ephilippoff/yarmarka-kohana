<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Objectload extends ORM {

	protected $_table_name = 'objectload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

	/*
		0 - defaul - endpoint
		1 - on_moderation
		2 - true_moderation
		3 - false_moderation - endpoint
		4 - in order 
		5 - finished - endpoint;
	*/
	function set_state($state = 0, $comment = NULL)
	{
		if (!$this->loaded())
			return;

		$this->state = $state;
		$this->comment = $comment;
		$this->update();
	}

	function get_statistic()
	{
		if (!$this->loaded())
			return;

		return unserialize($this->statistic);
	}


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
