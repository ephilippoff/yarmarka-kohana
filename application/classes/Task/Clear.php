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
		$this->set_category_urls();
	}

	function clear_search_url_cache()
	{
		ORM::factory('Search_Url_Cache')
				->where("created_on","<=", DB::expr("CURRENT_TIMESTAMP-INTERVAL '14 days'"))
				->delete_all();
	}

	function set_category_urls()
	{
		$categories = ORM::factory('Category')->find_all();
		foreach ($categories as $category) {
			$url = Search_Url::get_uri_category_segment($category->id);
			$category->url = $url;
			$category->save();
		}
		
	}
}