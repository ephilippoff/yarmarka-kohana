<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model_Seo 
 * 
 * @uses ORM
 * @package 
 * @copyright 2012
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Seo extends ORM
{

	protected static $secret = 'secret_#ode123ocvxvhias0a!&sdf';

	protected $_table_name = 'seo_surgut';

	protected static $_cache = array();

	public function rules()
	{
		return array(
			'url' => array(
				array('not_empty'),
				array(array($this, 'unique'), array('url', ':value')),
			),
		);

	}

	public function filters()
	{
		return array(
			TRUE	=> array(
				array('trim')
			),
			'url'	=> array(
				array(array($this, 'filter_url'))
			),
		);
	}

	public function filter_url($url)
	{
		return '/'.trim(trim($url), '/');
	}

	public function by_hashed_url($url){
		return $this->where("hash","=",$this->hash($url));
	}

	public static function hash($url)
	{	if (!$url) return FALSE;
		return sha1($url.self::$secret);
	}

	public function save_seo($pattern, $url, $category,  $city)
	{
		$this->where("hash","=", $this->hash($url))->find();

		$this->url = $url;
		$this->hash = $this->hash($url);
		$this->pattern_id = $pattern->id;
		$this->city_id = ($city) ? $city->id : 0;
		$this->category_id = ($category) ? $category->id : 0;


		$seo_params = array();
		foreach (array("h1","title","description","footer","anchor") as $value) {
			$seo_params[$value] = Seo::preformat_pattern($pattern->{$value});
		}
		$this->params = serialize($seo_params);
		$this->save();

		return $seo_params; 
	}
}
