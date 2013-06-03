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
	protected $_table_name = 'seo';

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
}
