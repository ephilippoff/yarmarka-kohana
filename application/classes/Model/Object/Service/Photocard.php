<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Service_Photocard extends ORM
{
	protected $_table_name = 'object_service_photocard';

	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

	//Удаление старых объявлений
	public function clear_old($days = 14)
	{
		$days = (int)$days ? (int)$days : 14;		
		
		return DB::delete('object_service_photocard')
				->where('id', 'in', DB::expr("(SELECT id 
											   FROM object_service_photocard 
											   WHERE NOW() >= date_created + interval '{$days} days'
											   AND type = 2
											   ORDER BY date_created LIMIT 1)"))
				->where('type', '=', 2)
			->execute();		
	}
	
	public function with_data()
	{
		return $this->select(array('object_attachment.filename', 'main_image_filename'))
			->select(array('object.title', 'object_title'))
			->select(array('category.title', 'category_title'))
			->select(array('city.title', 'city_title'))
			->join('object', 'left')
			->on('object_service_photocard.object_id', '=', 'object.id')
			->join('category', 'left')
			->on('object.category', '=', 'category.id')	
			->join('city', 'left')
			->on('object.city_id', '=', 'city.id')				
			->join('object_attachment', 'left')
			->on('object.main_image_id', '=', 'object_attachment.id');
	}	
}

