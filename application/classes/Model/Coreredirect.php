<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Coreredirect extends ORM
{
	protected $_table_name = 'core_redirect';

	public function rules()
	{
		return array(
			'source' => array(array('not_empty'),),
			'destination' => array(array('not_empty'),),
			'number' => array(array('digit'), array('not_empty'),),
			'use_white_ip' => array(array('digit'),),
		);
	}	

	public function filters()
	{
		return array(
			'source' => array(array('trim'),),
			'destination' => array(array('trim'),),
			'number' => array(array('trim'),),
			'use_white_ip' => array(array('intval'),),			
		);
	}	
}

/* End of file Attribute.php */
/* Location: ./application/classes/Model/Attribute.php */