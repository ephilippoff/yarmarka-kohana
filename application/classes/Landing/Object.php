<?php defined('SYSPATH') OR die('No direct script access.');

class Landing_Object extends Landing {

	public $object;
	public $user;
	public $attributes;
	public $location;
	public $contacts;
	public $pricerows;
	public $images;
	public $video;

	private $_object;

	public function __construct(ORM $object)
	{
		$this->_object = $object;
		$this->init();

		return $this;
	}

	public function init()
	{
		$this->object = $this->_object->as_array();
		$this->user = ORM::factory('User',$this->_object->author)->as_array();
		$this->attributes = $this->_object->get_attributes()->as_array();
		$this->contacts = $this->getContacts( ORM::factory('Contact')
								->where_object_id($this->_object->id)
								->order_by("id")
								->find_all()
								->as_array() );
		$this->images = $this->getImages( ORM::factory('Object_Attachment')
												->where("object_id","=",$this->_object->id)
												->where("type","=","0")
												->order_by("id")
												->find_all()
												->as_array() );
		$this->location = ORM::factory('Location', $this->_object->location_id)->as_array();

		$this->pricerows = $this->getPricelist( ORM::factory('Object_Priceload')
													->where("object_id","=",$this->_object->id)
													->find() ); 
	}

	private function getPricelist($object_priceload)
	{
		$result = array();

		if (!$object_priceload->loaded())
			return $result;

		$pi = ORM::factory('Priceload_Index')
				->where("priceload_id","=",$object_priceload->priceload_id)
				->limit(50)
				->order_by("id")
				->cached(Date::DAY)
				->find_all()
				->as_array();

		foreach ($pi as $row) {
			$result[] = $row->as_array();
		}

		return $result;
	}

	private function getContacts($contacts)
	{
		$result = array();
		if (!count($contacts))
			return $result;

		foreach ($contacts as $contact) {
			$result[] = $contact->as_array();
		}
		return $result;
	}

	private function getImages($_images)
	{
		$result = array();

		if (!count($_images))
			return $result;
		

		$images = $_images;

		$result["logo"] = array_shift($images)->as_array();
		$result["main"] = array_shift($images)->as_array();

		$result["other"] = array();
		foreach ($images as $image) {
			$result["other"][] = $image->as_array();
		}
		return $result;
	}

}