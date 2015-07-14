<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search_Url_Cache extends ORM {

	protected static $secret = 'secret_#ode123ocvxvhias0a!&sdf';
	protected $_table_name = 'search_url_cache';

	public function save_search_info($info, $url, $sql)
	{
		$hash_url = $this->hash($url);
		$sql = $this->prepare_sql($sql);

		if (!$sql) {
			return;
		}

		$hash_sql = $this->hash($sql);
		$serialized_info = serialize($info);
		
		$this->where("hash","=",$hash_url)->find();

		$this->hash = $hash_url;
		$this->url = $url;
		$this->params = $serialized_info;

		$this->sql = $sql;
		$this->hash_sql = $hash_sql;
		$this->save();
	}

	public function get_search_info($url)
	{
		$hash = $this->hash($url);
		return $this->where("hash","=",$hash);
	}

	public static function hash($url)
	{	if (!$url) return FALSE;
		return sha1($url.self::$secret);
	}

	public static function prepare_sql($sql = "")
	{
		preg_match("/FROM \"vw_objectcompiled\" AS \"o\" (.*) ORDER BY/", $sql, $match);
		if (count($match) == 2){
			return $match[1];
		}
	}

}

