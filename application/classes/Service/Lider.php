<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Lider extends Service
{
	const LIDER_SETTING_NAME = 'premium';
	const LIDER_DAYS = 7;

	protected $_name = "lider";
	protected $_title = "Лидер";
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
		Service_Lider::apply_service($orderItem->object_id);
		self::saveServiceInfoToCompiled($orderItem);
	}

	static function apply_service($object_id, $category_id = NULL, $user = NULL)
	{
		$object = ORM::factory('Object', $object_id);

		if (!$object->loaded()) return FALSE;

		if (!$category_id) 
			$category_id = $object->category;

		if (!$user)
			$user = Auth::instance()->get_user();

		$or = ORM::factory('Object_Service_Photocard')
					->where("object_id", "=", $object_id)
					->where("category_id", "=", $category_id)
					->find();
		$or->object_id = $object_id;
		$or->category_id = $category_id;
		$or->active = 1;
		$or->date_expiration = DB::expr("(NOW() + INTERVAL '".Service_Lider::LIDER_DAYS." days')");
		$or->save();

		return TRUE;
	}
}