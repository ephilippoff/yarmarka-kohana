<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Reklama extends ORM {

	protected $_table_name = 'reklama';
	
	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
			),
			'link' => array(
				array('not_empty'),
			),
		);
	}

	public function filters()
	{
		return array(
			'title' => array(
				array('trim'),
			),
			'seo_name' => array(
				array('trim'),
			),
		);
	}

}