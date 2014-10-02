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
					->cached(Date::WEEK)
					->find_all();
		}
		
		foreach ($_c as $row)
		{
			
			if ($category->get_count_childs($row->id) > 0)
			{
				$data[$row->id] =  $row->title;	
			} else {
				$data[$row->id] = array ( 0 => array (
													"title" => $row->title, 
													"title_auto" => $row->title_auto_fill,
													"text_required" =>  $row->text_required) );

				$data[$row->id] = array_merge($data[$row->id],  self::getElements((int) $row->id));
			}
		}
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
				->cached(Date::WEEK)
				->find_all();

		foreach ($ar as $relation)
		{
			$reference = ORM::factory('Reference')
						->where("id", "=", $relation->reference_id)
						->cached(Date::WEEK)
						->find();

			$attribute = ORM::factory('Attribute')
						->where("id", "=", $reference->attribute)
						->cached(Date::WEEK)
						->find();

			$rel_id = "_".$relation->reference_id;
			$data[$rel_id] = Array();
			$elements = Array();
			$type = NULL;

			switch ($attribute->type) {
				case 'integer':
				case 'numeric':
					$type = $attribute->type;

					$options = $relation->options;
					try 
					{
						if ($options)
						{
							$type = "ilist";
							$options  = self::parseOptionsIntNumAttribute($options);
							$elements = self::genValuesForIntNumAttribute($options);
						}
					} 
						catch(Exception $e)
					{

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
								->cached(Date::WEEK)
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
		$_min = explode("=", $min);
		$_max = explode("=", $max);
		$_step = explode("=", $step);

		$options["min"] = $_min[1];
		
		$options["max"] = $_max[1];
		if ($_max[1] == "year")
			$options["max"] = date("Y");
		
		$options["step"] = $_step[1];

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