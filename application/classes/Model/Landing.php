<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Landing extends ORM {

	protected $_table_name = 'landing';
	
	public function rules()
	{
		return array(
			'domain' => array(
				array('not_empty'),
			),
			'object_id' => array(
				array('not_empty'), array('digit')
			),
		);
	}	
	
	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);
	

}
