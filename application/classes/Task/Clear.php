<?php defined('SYSPATH') or die('No direct script access.');


class Task_Clear extends Minion_Task
{
	protected $_options = array(
		'limit' => 1000,
		'type'	=> NULL,
	);

	protected function _execute(array $params)
	{
		$limit 	= $params['limit'];
		$type 	= $params['type'];

		$this->clear_search_url_cache();
	}

	function clear_search_url_cache()
	{
		ORM::factory('Search_Url_Cache')
				->where("created_on","<=", DB::expr("CURRENT_TIMESTAMP-INTERVAL '7 days'"))
				->delete_all();
	}

}