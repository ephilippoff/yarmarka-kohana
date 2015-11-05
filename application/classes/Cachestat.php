<?php defined('SYSPATH') OR die('No direct access allowed.');

class Cachestat  {

	protected $_cached_key = 'cached';

	static function factory($name = '')
	{
		return new Cachestat($name);
	}

	function __construct($name = '')
	{
		$this->_cached_key = $name."_".$this->_cached_key."_".Kohana::$config->load("common.main_domain");
	}

	function add($key, $info)
	{
		$data = Cache::instance('memcache')->get($this->_cached_key);
		if (!$data)
		{
			$data = array();
			$data[$key] = $info;
		} else {
			$data = unserialize($data);
			$data[$key] = $info;
		}
		Cache::instance('memcache')->set($this->_cached_key, serialize($data), 60*60*48);
	}

	function fetch_search($callback)
	{
		$categories = ORM::factory('Category')->find_all();

		foreach ($categories as $category) {
			$data = Cache::instance('memcache')->get($category->id.$this->_cached_key);
			if ($data) {
				$data = unserialize($data);
				Cache::instance('memcache')->delete($category->id.$this->_cached_key);
				foreach ($data as $data_item) {
					$callback($data_item);
				}
			}
		}
	}

	function fetch($delete = FALSE)
	{
		$data = Cache::instance('memcache')->get($this->_cached_key);
		if ($data)
		{
			if ($delete) {
				Cache::instance('memcache')->delete($this->_cached_key);
			}
			$data = unserialize($data);
			$data = $data[0];
		}
		return $data;
	}

	function fetch_all($delete = FALSE)
	{
		$data = Cache::instance('memcache')->get($this->_cached_key);
		if ($data)
		{
			if ($delete) {
				Cache::instance('memcache')->delete($this->_cached_key);
			}
			$data = unserialize($data);
		}
		return $data;
	}
}