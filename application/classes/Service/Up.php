<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Up extends Service
{
	protected $_name = "up";
	protected $_title = "Подъем";
	protected $_is_multiple = FALSE;

	public function __construct($param = NULL)
	{
		$this->_initialize();
	}

	public function get()
	{

		return array(
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => $this->getPrice()
		);
	}

	public function apply($orderItem)
	{
		Service_Up::apply_service($orderItem->object_id);
		self::saveServiceInfoToCompiled($orderItem);
	}

	static function apply_service($object_id)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;

		$object->date_created = DB::expr("NOW()");
		$object->save();

		$or = ORM::factory('Object_Service_Up')
					->where("object_id", "=", $object_id)
					->find();

		$or->object_id = $object_id;
		$or->save();

		return TRUE;
	}
}