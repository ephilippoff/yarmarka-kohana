<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search_Url_Cache extends ORM {

	protected static $secret = 'secret_#ode123ocvxvhias0a!&sdf';
	protected $_table_name = 'search_url_cache';

	public function save_search_info($info, $url)
	{
		$hash = $this->hash_url($url);
		$serialized_info = serialize($info);
		
		$this->where("hash","=",$hash)->find();

		$this->hash = $hash;
		$this->url = $url;
		$this->params = $serialized_info;
		$this->save();
	}

	public function get_search_info($url)
	{
		$hash = $this->hash_url($url);
		return $this->where("hash","=",$hash);
	}

	public static function hash_url($url)
	{	if (!$url) return FALSE;
		return sha1($url.self::$secret);
	}

}

