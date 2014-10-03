<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Facade to form elements and references
*/
class Forms
{
	public static function get_by_category($category_id, $params)
	{echo Debug::vars($params);
		$elements = array();
		$ar = ORM::factory('Attribute_Relation')
					->where("category_id","=", $category_id)
					->order_by("weight","asc")
					->cached(Date::WEEK)
					->find_all();
		foreach ($ar as $relation) {
			$element = new Obj();
			$element->reference_id = $relation->reference_id;

			$ar_parent = NULL;
			if ($relation->parent_id)
				$ar_parent = ORM::factory('Attribute_Relation')
								->where("id","=", $relation->parent_id)
								->cached(Date::WEEK)
								->find();

			if (array_key_exists("param_".$relation->reference_id, (array) $params) AND $relation->is_required)
				$element->is_required  = $relation->is_required;
			
			elseif ($ar_parent AND $relation->is_required 
									AND array_key_exists("param_".$ar_parent->reference_id, (array) $params) 
											AND $params->{"param_".$ar_parent->reference_id} == $relation->parent_element_id)
				$element->is_required  = $relation->is_required;
			
			elseif ($ar_parent AND $relation->is_required 
									AND array_key_exists("param_".$ar_parent->reference_id, (array) $params) 
										AND is_array($params->{"param_".$ar_parent->reference_id})
											AND in_array($relation->parent_element_id, $params->{"param_".$ar_parent->reference_id}))
				$element->is_required  = $relation->is_required;

			$reference = ORM::factory('Reference')
							->select("attribute.title", "attribute.type", "attribute.solid_size", 
													"attribute.frac_size", "attribute.max_text_length")
							->join("attribute","left")
								->on("reference.attribute","=","attribute.id")
							->where("reference.id","=", $relation->reference_id)
							->cached(Date::WEEK)
							->find();

			$element->attribute_title 			= $reference->title;
			$element->attribute_type 			= $reference->type;
			$element->attribute_solid_size 	= $reference->solid_size;
			$element->attribute_frac_size 		= $reference->frac_size;
			$element->attribute_max_text_length = $reference->max_text_length;
			$element->is_range 				    = FALSE;

			$elements[] = $element;
		}
echo Debug::vars($elements);
		return $elements; 
	}
}

/* End of file Forms.php */
/* Location: ./application/classes/Forms.php */