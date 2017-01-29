<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search_Url_Cache extends ORM {

	protected static $secret = 'secret_#ode123ocvxvhias0a!&sdf';
	protected $_table_name = 'search_url_cache';

	public function save_search_info($info, $url, $canonical_url, $sql, $count)
	{
		$hash_url = $this->hash($url);
		$sql = $this->prepare_sql($sql, "\"vw_objectcompiled\" AS \"o\"", "ORDER BY");

		if (!$sql) {
			return;
		}

		$hash_sql = $this->hash($sql);
		$serialized_info = serialize($info);
		
		$this->where("hash","=",$hash_url)->find();

		$this->hash = $hash_url;
		$this->url = trim($url,"/");
		$this->params = $serialized_info;

		$this->sql = $sql;
		$this->hash_sql = $hash_sql;

		$this->count = (int) $count;
		$this->canonical_url = $canonical_url;
		$this->save();

		return $this;
	}

	public function get_search_info($url)
	{
		$hash = $this->hash($url);
		return $this->where("hash","=",$hash);
	}

	public function get_search_info_by_urls($urls = array(), $query = NULL)
	{
		if (!$query) {
			$query = $this;
		}
		$hashs = array();
		foreach ($urls as $url) {
			$hashs[] = $this->hash($url);
		}
		return $query->where("hash","IN",$hashs);
	}

	public function get_count_for_categories($domain, $category_url, $categories = array())
	{
		$result = array();

		$suc = DB::select("count","url")->from("search_url_cache");

		$urls = array_map(function($category) use ($domain, $category_url) {
		    $url = "";
		    if ($category->attribute) {
		        $url =  $domain."/".$category_url."/".$category->url;
		    } else {
		        $url =  $domain."/".$category->url;
		    }
		    return trim(trim($url, '/'));
		}, $categories);

		if (count($urls) > 0) {
		    $suc = ORM::factory('Search_Url_Cache')->get_search_info_by_urls($urls, $suc);
		    $suc = $suc->execute()->as_array();
		    foreach ($suc as $item) {
		        $result[$item["url"]] = $item["count"];
		    };
		}


		return $result;
	}

	public static function hash($url)
	{	if (!$url) return FALSE;
		return sha1($url.self::$secret);
	}

	public static function prepare_sql($sql = "", $table_name = NULL, $limit_str = NULL)
	{
		return Search::prepare_sql($sql, $table_name, $limit_str);
	}

}

