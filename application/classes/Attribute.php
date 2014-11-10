<?php defined('SYSPATH') OR die('No direct script access.');

class Attribute {

	static function getData($category_id = 0){

		$data = Array();
		$category = ORM::factory('Category');	

		if ($category_id > 0 )
		{
			$_c = $category->where("id","=",$category_id)
								->cached(Date::WEEK)
								->find_all();	
		} else {
			$_c = $category->order_by("id")
					->cached(Date::WEEK, array("add","search","category"))
					->find_all();
		}

		$_address_precisions = Kohana::$config->load("maps.address_precision");
		
		foreach ($_c as $row)
		{
			$address_precisions = array();
			foreach ($_address_precisions as $pricision) {
				if ($row->id == $pricision["category_id"])
					$address_precisions[] = $pricision;
			}
			
			if ($category->get_count_childs($row->id) > 0)
			{
				$data[$row->id] =  $row->title;	
			} else {

				$category_params =  array (
													"title" => $row->title, 
													"title_auto" => $row->title_auto_fill,
													"text_required" =>  $row->text_required);


				$options = Kohana::$config->load("category.".$row->id);
				if ($options)
					foreach ($options as $key => $value) {
						if (in_array($key, array(	"one_mobile_phone",
													"max_count",
													"description",
													"price_enabled")))
							$category_params[$key] = $value;
					}

				if ($options)
					$category_params = array_merge($category_params, $options);

				if (count($address_precisions)>0)
					$category_params["address_precisions"] = $address_precisions;

				$data[$row->id] = array ( 0 => $category_params );
				$data[$row->id] = array_merge($data[$row->id],  self::getElements((int) $row->id));
			}
		}

		$data["descriptions"] = Kohana::$config->load("descriptions");
		return $data;
	}

	static function getElements($category_id, $parent_id = NULL, $element_id = NULL, $parent_element_id = NULL)
	{
		$data = Array();

		$attribute_relation = ORM::factory('Attribute_Relation');
		
		$ar = $attribute_relation
				->join('reference', 'left')
					->on('attribute_relation.reference_id', '=', 'reference.id')
				->where("reference.category","=",$category_id)
				->where("attribute_relation.parent_id","=",$parent_id)
				->where("attribute_relation.parent_element_id","=",$element_id)
				->order_by("attribute_relation.weight")
				->cached(Date::WEEK, array("add","search","relation"))
				->find_all();

		foreach ($ar as $relation)
		{
			$reference = ORM::factory('Reference')
						->where("id", "=", $relation->reference_id)
						->cached(Date::WEEK, array("add","search","relation"))
						->find();

			$attribute = ORM::factory('Attribute')
						->where("id", "=", $reference->attribute)
						->cached(Date::WEEK, array("add","search","relation"))
						->find();

			$rel_id = "_".$relation->reference_id;
			$data[$rel_id] = Array();
			$elements = Array();
			$type = NULL;

			switch ($attribute->type) {
				case 'integer':
				case 'numeric':
					$type = $attribute->type;

					if ($relation->is_ilist($relation->options))
					{
						
						$options  = self::parseOptionsIntNumAttribute($relation->options);
						if ($options)
						{
							$elements = self::genValuesForIntNumAttribute($options);
							$type = "ilist";
						}
					}
				break;				
				default:
					$type = $attribute->type;

					$elements = Array();
					$ae = ORM::factory('Attribute_Element')
								->where("attribute","=",$attribute->id);
					
					if ($relation->options == "subelements")
						$ae = $ae->where("parent_element","=",$parent_element_id);
					
					$ae = $ae->order_by("weight")
								->order_by("title")
								->cached(Date::WEEK, array("add","search","element"))
								->find_all();

					foreach ($ae as $element)
					{		
						$el_id = "_".$element->id;
						$child = self:: getElements($category_id, $relation->id, $element->id, $element->id);
						if (count($child)>0)
						{
							$elements[$el_id] = Array(0 => Array ( "title" => $element->title));
							$elements[$el_id] = array_merge($elements[$el_id],  $child);
						} else {
							$elements[$el_id] = $element->title;
						}
					}

				break;
			}

			$data[$rel_id] = Array(0 => Array ( 
												"id" => "param_".$relation->reference_id,
												"title" => $attribute->title,  
												"type" =>  $type, 
												"ref_id" => $relation->reference_id, 
												"custom" => $relation->custom,
												"options" => $relation->options,
												"weight" => $relation->weight,
												"is_textarea" => $attribute->is_textarea,
												"is_required" => $relation->is_required,
												"unit"		  => $attribute->unit
											)
										);
			$data[$rel_id] = array_merge($data[$rel_id], $elements);
			
		}

		return $data;
	}

	static function parseElementLevel($data, $params = NULL, &$list){
		$info = $data[0];
		unset($data[0]);
		$values = Array();
		$value = NULL;

		$ref_id = $info["ref_id"];

		$value_exist = array_key_exists($ref_id, $params);

		if ( $value_exist)
			$value = $params[ $ref_id ];

		if( $value_exist AND $info["type"] == "ilist" AND  substr($value, 0, 1) <> "_"){
			//if ($value)
			//	$value = "_".$value;
		}
		
		$data_elements = Array();
		foreach($data as $key => $element)
		{
			if (is_array($element))
			{
				$values[$key] = $element[0]["title"];
				if ($value == $key OR (is_array($value) AND in_array($key, $value))) { $data_elements[] = $element; }
			} else {
				$values[$key] = $element;
			}
		}

		$list[] =  Array(
				"title" 	=> $info["title"],
				"type" 		=> $info["type"],
				"custom" 	=> $info["custom"],
				"options" 	=> $info["options"],
				"weight" 	=> $info["weight"],
				"is_required"=> $info["is_required"],
				"is_textarea"=> $info["is_textarea"],
				"unit"		=> $info["unit"],
				"name" 		=> "param_".$ref_id,
				"values"	=> $values,
				"value" 	=> $value
		);

		if ($value_exist)
			foreach ($data_elements as $data)
				if (array_key_exists(0, $data)) 
					self::parseAttributeLevel($data, $params, $list); 
			
	}

	static function parseAttributeLevel($data, $params = NULL, &$list = Array()){
		$info = $data[0];
		unset($data[0]);

		foreach($data as $key => $element){	
			self::parseElementLevel($element, $params, $list);
		}

		return $list;
	}

	static function parseOptionsIntNumAttribute($options_str)
	{
		@list($min, $max, $step) = explode(",", $options_str);
		//if (!is_array($min) OR !is_array($max) OR !is_array($step))
		//	return FALSE;

		@list($min, $_min) = explode("=", $min);
		@list($max, $_max) = explode("=", $max);
		@list($step, $_step) = explode("=", $step);

		if (!$_min OR !$_max OR !$_step)
			return FALSE;

		$options["min"] = $_min;
		
		$options["max"] = $_max;
		if ($_max == "year")
			$options["max"] = date("Y");
		
		$options["step"] = $_step;

		return $options;
	}

	static function genValuesForIntNumAttribute($options)
	{
		$return = Array();
		for ($i = $options["min"]; $i<= $options["max"]; $i = $i + $options["step"])
		{
			$return["0".$i] = $i;
		}
		return $return;
	}
	
}