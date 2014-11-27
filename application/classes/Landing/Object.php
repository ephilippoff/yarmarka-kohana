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
		$object->category_obj;
		$this->_object = $object;
		$this->init();

		return $this;
	}

	public function init()
	{	

		$this->compiled = NULL;
		$compiled = ORM::factory('Object_Compiled')
							->where("object_id","=",$this->_object->id)
							->find()
							->compiled;
		if ($compiled)
			$this->compiled = unserialize($compiled);

		$this->images 		= $this->getImages( $this->compiled["photo"] );
		$this->video 		= $this->getImages( $this->compiled["video"] );
		$this->contacts 	= (isset($this->compiled["contacts"])) ? $this->compiled["contacts"] : NULL;
		$this->attributes 	= $this->getAttributes( (isset($this->compiled["attributes"])) ? $this->compiled["attributes"] : NULL );
		$this->address 		= (isset($this->compiled["address"])) ? $this->compiled["address"] : NULL;
		$this->lat 			= (isset($this->compiled["lat"])) ? $this->compiled["lat"] : NULL;
		$this->lon 		 	= (isset($this->compiled["lon"])) ? $this->compiled["lon"] : NULL;
		$this->priceload	= (isset($this->compiled["pricelist"])) ? ORM::factory('Priceload',$this->compiled["pricelist"]) : NULL;
		$this->pricerows 	= $this->getPricelist( $this->priceload ,(isset($this->compiled["pricelist"])) ? $this->compiled["pricelist"] : NULL );

		$this->object 	= $this->_object->as_array();
		$this->user 	= ORM::factory('User',$this->_object->author)->as_array();
		$this->favorite = Auth::instance()->get_user() ? $this->_object->get_favorite(Auth::instance()->get_user()->id) : null;
	}

	private function getAttributes($attributes)
	{
		if (!$attributes)
			return;

		$result = array();
		foreach ($attributes as $attribute) {
			
			$_attribute = array();
			$_attribute["title"] = $attribute["_attribute"]["title"];

			switch ($attribute["_type"]) {
				case "List":
					$_attribute["value"] = $attribute["_element"]["title"];
				break;
				case "Integer":
				case "Numeric":
					$_attribute["value"] = $attribute["value_min"];
				break;
				default:
					$_attribute["value"] = $attribute["value"];
				break;
			}
			$result[] = $_attribute;
		}	

		return $result;
	}

	private function getPricelist($priceload, $id)
	{
		if (!$id)
			return;

		$config = unserialize($priceload->config);
		$columns = explode(",",$config["columns"]);

		$result = array();

		$pi = ORM::factory('Priceload_Index')
				->where("priceload_id","=",$id)
				->limit(50)
				->order_by("id")
				->cached(Date::DAY)
				->find_all()
				->as_array();

		foreach ($pi as $row) {
			$_row = $row->as_array();
			$_row["titles"] =  array();
			$_row["values"] =  array(); 

			$full = unserialize($row->full);

			foreach($columns as $column){
				if (isset($config[$column."_type"]) AND $config[$column."_type"] == "info"){
					//$_row["titles"][] = $config[$column."_title"];
					//$_row["titles"][] = "Описание";
					//$_row["titles"][] = "Цена";
					$_row["values"][] = $full[$column];					
				}				
			}
			$result[] = $_row;
		}

		return $result;
	}

	private function getContacts($contacts)
	{
		$result = array();
		if (!count($contacts))
			return $result;

		foreach ($contacts as $contact) {
			$contact->contact_type;
			$result[] = $contact->as_array();
		}
		return $result;
	}

	private function getImages($_images)
	{
		$result = array();

		if (!count($_images))
			return $result;
		

		$images = array_reverse($_images);

		$result["logo"] = Imageci::getSavePaths(array_shift($images));
		$result["main"] = Imageci::getSavePaths(array_shift($images));

		$result["other"] = array();
		foreach ($images as $image) {
			$result["other"][] = Imageci::getSavePaths($image);
		}
		return $result;
	}

}