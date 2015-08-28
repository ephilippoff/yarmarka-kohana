<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Callbackrequest extends ORM
{
	protected $_table_name = 'callback_request';

	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);
	
	public function rules()
	{
		return array(
			'fio' => array(
				array('not_empty'),
			),
			'phone' => array(
				array('not_empty'),
			),
		);
	}

	public function filters()
	{
		return array(
			'fio' => array(
				array('trim'), 
				array('strip_tags'),
			),
			'phone' => array(
				array('trim'), 
				array('strip_tags'),
			),
			'object_id' => array(
				array('intval')
			),
			'comment' => array(
				array('trim'),
			),			
		);
	}
	
	public function labels()
	{
		return array(
			'fio'   => 'Ф.И.О',
			'phone' => 'Номер телефона',
		);		
	}
	
	public function with_objects()
	{
		return $this->select(array('object.title', 'object_title'))
			->join('object', 'left')
			->on('callbackrequest.object_id', '=', 'object.id');
	}
	
}