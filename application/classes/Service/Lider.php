<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Lider extends Service
{
	const LIDER_SETTING_NAME = 'premium';
	const LIDER_DAYS = 7;

	protected $_name = "lider";
	protected $_title = "Лидер";
	protected $_is_multiple = TRUE;
	public $_period = 7;

	public function __construct($object_id = NULL)
	{
		$object = ORM::factory('Object',$object_id);
		if ($object->loaded()) {
			$this->object($object);
		}
		$this->_initialize();
	}

	public function period($period = NULL)
    {
    	if (!$period) return $this->_period;
        $this->_period = $period;
        return $this;
    }

	public function get()
	{
		return array(
			"period" => $this->period(),
			"city" => $this->city(),
			"category" => $this->category(),
			"name" => $this->_name,
			"title" => $this->_title,
			"price" => ($this->_is_multiple) ? $this->getPriceMultiple() : $this->getPrice()
		);
	}

	public function set_params($params = array())
	{
		parent::set_params($params);
		$params = new Obj($params);

		if ($params->period) {
			$this->period($params->period);
		}
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

	public function get_params_description($params = array())
	{
		$params = new Obj($params);
		return "Количество: ".$params->quantity;
	}
}