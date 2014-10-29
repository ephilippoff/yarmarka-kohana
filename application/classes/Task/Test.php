<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(
		"number" => NULL,
		"code" => NULL
	);

	protected function _execute(array $params)
	{
		$number = $params["number"];
		$code = $params["code"];
	
		Minion_CLI::write('result: '.Text::format_contact($number, $code));

	}

}
