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

		$this->compiled = NULL;

		if ($cachedLanding = Cache::instance('memcache')->get("landing:{$this->_object->id}"))
		{
			$this->unserialize($cachedLanding);
			return;
		}

		$compiled = ORM::factory('Object_Compiled')
							->where("object_id","=",$this->_object->id)
							->find()							
							->compiled;

		if ($compiled)
			$this->compiled = unserialize($compiled);

		$this->images 		= $this->getImages( $this->compiled["photo"] );
		$this->video 		= $this->compiled["video"];
		$this->contacts 	= (isset($this->compiled["contacts"])) ? $this->compiled["contacts"] : NULL;
		$this->attributes 	= $this->getAttributes( (isset($this->compiled["attributes"])) ? $this->compiled["attributes"] : NULL );
		$this->address 		= (isset($this->compiled["address"])) ? $this->compiled["address"] : NULL;
		$this->lat 			= (isset($this->compiled["lat"])) ? $this->compiled["lat"] : NULL;
		$this->lon 		 	= (isset($this->compiled["lon"])) ? $this->compiled["lon"] : NULL;
		$this->object 	= $this->_object->as_array();

		//Прайсы
		$simple_attributes = $hierarchy_attributes = NULL;
		$this->priceload =  ORM::factory('Priceload')
									->where("id","=",$this->compiled["pricelist"])
									->find();

		$priceload	= ORM::factory('Priceload')
									->where("id","=",$this->compiled["pricelist"])
									->where("table_name","IS NOT",DB::expr("NULL"))
									->find();
		
		if ($priceload->loaded())
		{
			

			$columns =  self::getPricelistColumns($priceload);
			$pricerows 	= self::getPricelist( $priceload ,$priceload->id );
			$pricerows_count 	= self::getPricelistCount($priceload->id);

			$simple_filters = ORM::factory('Priceload_Attribute')
												->where("priceload_id","=",$priceload->id)
												->where("type","=","simple")
												->find_all();
			$simple_filters = $this->getSimpleFilters($priceload->id, $simple_filters);

			$hierarchy_filter_id = ORM::factory('Priceload_Attribute')
											->where("priceload_id","=",$priceload->id)
											->where("type","=","hierarchy")
											->find()->id;

			$hierarchy_filters = ORM::factory('Priceload_Filter')
												->where("priceload_id","=",$priceload->id)
												->where("priceload_attribute_id","=",$hierarchy_filter_id)
												->where("parent_id","IS",NULL)
												->order_by("title","asc")
												->find_all();

			$this->pricelist = array(
					"object" => $this->object,
					"columns" => $columns,
					"priceload" => $priceload,
					"pricerows" => $pricerows,
					"pricerows_count" => $pricerows_count,
					"simple_filters" => $simple_filters,
					"hierarchy_filters" => $hierarchy_filters
				);

		}
		//----
		
		
		$this->user 	= ORM::factory('User')
									->where_cached("id","=",$this->_object->author,Date::DAY)
									->find()->as_array();

		$this->category = ORM::factory('Category')
									->where_cached("id","=",$this->_object->category,Date::DAY)
									->find()->as_array();

		$this->favorite = Auth::instance()->get_user() ? $this->_object->get_favorite(Auth::instance()->get_user()->id) : null;

		Cache::instance('memcache')->set("landing:{$this->_object->id}", $this->serialize());
	}

	private function getSimpleFilters($priceload_id, $attributes)
	{
		$result = array();
		foreach ($attributes as $attribute) {
			$params = $attribute->as_array();
			$values = array();
			$pf =  ORM::factory('Priceload_Filter')
												->where("priceload_id","=",$priceload_id)
												->where("priceload_attribute_id","=",$attribute->id)
												->find_all();
			foreach ($pf as $filter) {
				$values[$filter->id] =  array ( "title" => $filter->title, "count" => $filter->count);
			}
			$params["values"] = $values;
			$result[] = $params;
		}

		return $result;
	}

	private function getAttributes($attributes)
	{
		if (!$attributes)
			return;

		$result = array();
		foreach ($attributes as $attribute) {
			
			$_attribute = array();
			$_attribute["title"] = $attribute["_attribute"]["title"];
			$_attribute["seo_name"] = $attribute["_attribute"]["seo_name"];
			$type = $attribute["_attribute"]["type"];

			switch ($type) {
				case "list":
					$_attribute["value"] = $attribute["_element"]["title"];
				break;
				case "integer":
				case "numeric":
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

	public static function getPricelistColumns($priceload)
	{
		$result = array();
		$config = unserialize($priceload->config);
		$columns = explode(",",$config["columns"]);

		foreach($columns as $column){
			if (isset($config[$column."_type"]) AND $config[$column."_type"] == "info"){
				$result[] = $config[$column."_title"];
			}
		}
		return $result;
	}

	public static function getPricelistCount($priceload_id, $attributes =  NULL, $limit = 50, $offset = 0)
	{
		return $pi = ORM::factory('Priceload_Index')
				->where("priceload_id","=",$priceload_id)
				->search($attributes)
				//->cached(Date::DAY)
				->count_all();
	}

	public static function getPricelist($priceload, $id, $attributes =  NULL, $limit = 50, $offset = 0)
	{
		if (!$id)
			return;

		$config = unserialize($priceload->config);
		$columns = explode(",",$config["columns"]);

		$result = array();

		$pi = ORM::factory('Priceload_Index')
				->where("priceload_id","=",$id)
				->search($attributes)
				->limit($limit)
				->offset($offset)
				->order_by("id")
				//->cached(Date::DAY)
				->find_all()
				->as_array();

		foreach ($pi as $row) {
			$_row = $row->as_array();
			$_row["values"] =  array(); 

			$full = unserialize($row->full);

			foreach($columns as $column){
				if (isset($config[$column."_type"]) AND $config[$column."_type"] == "info"){
					$_row["values"][] = $full[$column];					
				}				
			}
			$result[] = $_row;

		}

		return $result;
	}

	public static function clearSphinxValues($priceload, $_rows)
	{
		$config = unserialize($priceload->config);
		$columns = explode(",",$config["columns"]);

		$rows = array();
		foreach ($_rows as $key => $row) {
			$full = $row["values"];
			$row["values"] = array();
			foreach($columns as $column){
				
				if (isset($config[$column."_type"]) AND $config[$column."_type"] == "info"){
					$row["values"][] = $full[$column];
				}
			}
			$_rows[$key]["values"] = $row["values"];

		}

		return $_rows;
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

		if (count($images))
			$result["logo"] = Imageci::getSavePaths(array_shift($images));
		if (count($images))
			$result["main"] = Imageci::getSavePaths(array_shift($images));

		$result["other"] = array();
		if (count($images))
			foreach ($images as $image) {
				$result["other"][] = Imageci::getSavePaths($image);
			}
		return $result;
	}

	private function serialize()
	{
		return get_object_vars($this);
	}

	private function unserialize($cache)
	{
		foreach ($cache as $key => $value) {
			$this->{$key} = $value;
		}

	}

	function __get($key)
	{
		if (property_exists($this, $key))
			return $key;
		else
			return NULL;
	}

}