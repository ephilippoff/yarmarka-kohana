<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Reason extends ORM {
	
	protected $_table_name = 'object_reason';
	
	public function rules()
	{
		return array(
			'full_text' => array(array('not_empty'),),
		);
	}

	public function filters()
	{
		return array(
			'full_text' => array(array('trim'),),
		);
	}	
}

/* End of file Reason.php */
/* Location: ./application/classes/Model/Object/Reason.php */