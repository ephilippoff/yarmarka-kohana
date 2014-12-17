<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Selection extends ORM
{
	protected $_table_name = 'object_selection';

	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

	//Удаление старых объявлений
	public function clear_old($days = 14)
	{
		$days = (int)$days ? (int)$days : 14;		
		
		return DB::delete('object_selection')
				->where('id', 'in', DB::expr("(SELECT id FROM object_selection where NOW() >= date_created + interval '{$days} days' ORDER BY date_created LIMIT 1)"))
			->execute();		
	}
}

