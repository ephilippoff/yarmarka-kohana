<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(
		"id" => NULL
	);

	protected function _execute(array $params)
	{


		$objectload = new Objectload(NULL, $params['id']);
		$objectload->sendReport($objectload_id);
	}

}
