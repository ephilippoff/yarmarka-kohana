<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Temp extends ORM {

	public $_table_name = '_temp';

	public function __construct($temp_table_name, $id = NULL)
	{
		$this->_table_name = '_temp_'.$temp_table_name;
		parent::__construct($id);
		ORM::$_column_cache[$this->_object_name] = NULL;
		
	}
} 