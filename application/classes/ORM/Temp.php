<?php defined('SYSPATH') OR die('No direct access allowed.');

/*
 Расширение ORM для временных таблиц созданных с помощью класса Temptable
*/
class ORM_Temp extends ORM {

	public static function factory($temp_table_name, $id = NULL)
	{
		// Set class name
		$model = 'Model_Temp';
		
		return new $model($temp_table_name, $id);
	}

}