<?php defined('SYSPATH') or die('No direct script access.');

class Cache_Memcache extends Kohana_Cache_Memcache {


	public function set($id, $data, $lifetime = 3600, $tag = NULL)
	{
		if ($tag AND $lifetime>3600)
		{
			$memcached = ORM::factory('Memcached');
			$memcached->key = $id;
			if (is_array($tag))
				$memcached->tag = implode(",", $tag);
			else 
				$memcached->tag = $tag;

			$memcached->expired = DB::expr("NOW() + interval '$lifetime second'") ;
			$memcached->save();
		}

		parent::set($id, $data, $lifetime);
	}

}