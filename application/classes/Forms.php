<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Facade to form elements and references
*/
class Forms
{
	public static function get_by_category($category_id, $params)
	{
		$elements = array();
		$ar = ORM::factory('Attribute_Relation')
					->where("category_id","=", $category_id)
					->order_by("weight","asc")
					->cached(Date::WEEK)
					->find_all();
		foreach ($ar as $relation) {
			$element = new Obj();
			$element->reference_id = $relation->reference_id;
			$element->is_ilist 	   = $relation->is_ilist($relation->options);

			$ar_parent = NULL;
			if ($relation->parent_id)
				$ar_parent = ORM::factory('Attribute_Relation',$relation->parent_id)->cached(Date::WEEK);

			/* Если элемент формы обязателен и он без родителей */
			if (!$relation->parent_id AND $relation->is_required)
				$element->is_required  = $relation->is_required;
			/* Если элемент формы обязателен и его родитель выбран, т.е. этот подчиненный элемент показан на форме */
			elseif ($ar_parent AND $relation->is_required 
									AND array_key_exists("param_".$ar_parent->reference_id, (array) $params) 
											AND $params->{"param_".$ar_parent->reference_id} == $relation->parent_element_id)
			{

				if ($ar_parent->parent_id)
				{
					$p_element_id = $ar_parent->parent_element_id;
					$ar_parent = ORM::factory('Attribute_Relation',$ar_parent->parent_id)->cached(Date::WEEK);
					if ($ar_parent AND $relation->is_required 
									AND array_key_exists("param_".$ar_parent->reference_id, (array) $params) 
											AND $params->{"param_".$ar_parent->reference_id} == $p_element_id)
						$element->is_required  = $relation->is_required;
				} else {
					$element->is_required  = $relation->is_required;
				}
			}
			/* Если элемент формы обязателен и его родитель выбран, т.е. этот подчиненный элемент показан на форме, 
							и в мультивыборе выбрано одно из значений который показывает этот элемент формы */
			elseif ($ar_parent AND $relation->is_required 
									AND array_key_exists("param_".$ar_parent->reference_id, (array) $params) 
										AND is_array($params->{"param_".$ar_parent->reference_id})
											AND in_array($relation->parent_element_id, $params->{"param_".$ar_parent->reference_id}))
				$element->is_required  = $relation->is_required;

			$reference =  ORM::factory('Reference')
							->with_attribute_by_id($relation->reference_id)
							->cached(Date::WEEK)
							->find();

			$element->attribute_title 			= $reference->title;
			$element->attribute_type 			= $reference->type;
			$element->attribute_solid_size 		= $reference->solid_size;
			$element->attribute_frac_size 		= $reference->frac_size;
			$element->attribute_max_text_length = $reference->max_text_length;
			$element->is_range 				    = FALSE;

			$elements[] = $element;
		}

		return $elements; 
	}
}

/* End of file Forms.php */
/* Location: ./application/classes/Forms.php */