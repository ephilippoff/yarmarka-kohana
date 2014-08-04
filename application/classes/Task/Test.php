<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(
		'count'	=> 1000,
	);

	protected function _execute(array $params)
	{
		$test = Num::rus_suffix("Предложение", $params['count']);
		Minion_CLI::write('Result:'.Minion_CLI::color($test, 'cyan'));
	}

}
