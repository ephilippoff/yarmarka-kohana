<?php defined('SYSPATH') or die('No direct script access.');

class Cache_Memcache extends Kohana_Cache_Memcache {

	private $env = "";

	function __construct(array $config)
	{
		if (Kohana::$environment == Kohana::DEVELOPMENT)
			$this->env = "dev_";
		parent::__construct($config);
	}

	public function set($id, $data, $lifetime = 3600, $tag = NULL)
	{
		$id = $this->env.$id;
		/*if ($tag AND $lifetime>3600)
		{
			$memcached = ORM::factory('Memcached');
			$memcached->key = $id;
			if (is_array($tag))
				$memcached->tag = implode(",", $tag);
			else 
				$memcached->tag = $tag;

			$memcached->expired = DB::expr("NOW() + interval '$lifetime second'") ;
			$memcached->save();
		}*/

		parent::set($id, $data, $lifetime);
	}

	public function get($id, $default = NULL)
	{
		$id = $this->env.$id;
		parent::get($id, $default);
	}

	public function delete($id, $timeout = 0)
	{
		$id = $this->env.$id;
		parent::delete($id, $timeout);
	}

	public function increment($id, $step = 1)
	{
		$id = $this->env.$id;
		parent::increment($id, $step);
	}

	public function decrement($id, $step = 1)
	{
		$id = $this->env.$id;
		parent::decrement($id, $step);
	}

}