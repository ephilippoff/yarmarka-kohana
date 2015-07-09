<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Objectcompiled extends Model_Object {

	protected $_table_name = 'vw_objectcompiled';

	public function get_compiled()
	{
		if (!$this->loaded()) return;
		
		return unserialize($this->compiled);
	}

}
