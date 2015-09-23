<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Callback_Request extends ORM {

	protected $_table_name = 'callback_request';

	public function rules()
	{
		return array(
			'fio' => array(array('not_empty'),),
			'phone' => array(array('not_empty'),),
		);
	}

} // End Access Model
