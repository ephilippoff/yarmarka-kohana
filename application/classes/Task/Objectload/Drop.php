<?php defined('SYSPATH') or die('No direct script access.');


class Task_ObjectLoad_Drop extends Minion_Task
{
	protected $_options = array(
		"days" => 7
	);

	protected function _execute(array $params)
	{
		$days 				= $params['days'];

		$ol = ORM::factory('Objectload')
				->where("created_on", "<=", DB::expr("CURRENT_DATE - interval '$days days'"))
				->find_all();
		foreach($ol as $load)
		{
			$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $load->id)
				->where("table_name","IS NOT",NULL)
				->find_all();
			foreach($of as $file)
			{
				try {
					Temptable::delete_table($file->table_name);
					$of_update = ORM::factory('Objectload_Files', $file->id);
					$of_update->table_name = NULL;
					$of_update->update();
				} catch (Exception $e)
				{
					Minion_CLI::write($e->getMessage());
				}
			}
		}
		

		Minion_CLI::write('success drop');

		//Temptable::delete_table($name);
	}

}
