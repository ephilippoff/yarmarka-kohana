<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Kupon_Requests extends ORM
{
	protected $_table_name = 'object_kupon_requests';

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
			->on('object_kupon_requests.object_id', '=', 'object.id');
	}
	
}