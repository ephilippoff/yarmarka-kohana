<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Service_Invoices 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Seo_Pattern extends ORM {

	protected $_table_name = 'seo_patterns';

	public function get_pattern_with_params($category_id, $query_params = array())
	{
		$params = FALSE;
		if (count(array_keys($query_params)))
		{
			$params = self::query_keys($query_params);
		}

		$this->where('category_id', "=",intval($category_id));

		if ($params) 
		{
			$this->where('params', "=",$params);
		}
		return $this;
	}

	public function get_pattern($category_id, $query_params) {
		
		$this->get_pattern_with_params($category_id, $query_params)->find();
		if ( !$this->loaded() AND count(array_keys($query_params)) ) {
			$this->where('category_id', "=",intval($category_id))->find();
		}
		return $this;
	}

	public static function query_keys($params)
	{
		return join('&', array_keys($params));
	}

} // End Service_Invoices Model