<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_City 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_City extends ORM {

	protected $_table_name = 'city';

	protected $_has_many = array(
		'users'	=> array(),
	);

	protected $_belongs_to = array(
		'region'	=> array(),
		'location'	=> array(),
	);

	public function get_url()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return 'http://'.$this->seo_name.'.'.Kohana::$config->load('common.main_domain');
	}

} // End City Model
