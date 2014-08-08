<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(
		'category'	=> 3,
		'list_ids'	=> Array(52 => 226.5),
		'similar_object_id' => 3402021
	);

	protected function _execute(array $params)
	{
		$test = Lib_PlacementAds_AddEdit::validate_between_parameters($params['category'],$params['list_ids'],$params['similar_object_id']);
		Minion_CLI::write('Result:'.Minion_CLI::color($test, 'cyan'));
	}

}
