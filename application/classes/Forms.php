<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Facade to form elements and references
*/
class Forms
{
	public static function get_by_category_and_type($category_id, $form_type = 'add')
	{
		/**
		 * @todo
		 * Из-за select кэширование работает не правильно, 
		 * не возвращаются поля которые перечислены в select, не правильно собирается объет Database_Result_Cached
		 * пока кэш отключен потом надо будет что-то думать
		 */
		return ORM::factory('Reference')
			->select('form_element.is_range', 'form_element.is_multiselect', 'form_element.editing', 
				'form_element.max', 'form_element.is_horizontal', 'form_element.is_autocomplete')
			->with('attribute_obj')
			->join('form_element', 'inner')
			->on('reference.id', '=', 'form_element.reference')
			->where('reference.category', '=', intval($category_id))
			->where('form_element.type', '=', $form_type)
			// ->cached(Date::HOUR)
			->find_all();
	}

	public static function get_category_conditions($category_id)
	{
		return ORM::factory('Reference_Conditions')
			->select(array('attribute_element.title', 'value_title'))
			->select(array('attribute.title', 'attribute.title'))
			->join('reference')
			->on('reference.id', '=', 'reference_conditions.reference')
			->join('attribute')
			->on('attribute.id', '=', 'reference.attribute')
			->join('attribute_element')
			->on('attribute.id', '=', 'attribute_element.attribute')
			->where('reference_conditions.for_reference', 'IN', 
				DB::expr('(SELECT id FROM reference WHERE reference.category = '.intval($category_id).') AND (reference_conditions.value_list = attribute_element.id or reference_conditions.value_list IS NULL)'))
			// ->cached(Date::HOUR)
			->find_all();
	}
}

/* End of file Forms.php */
/* Location: ./application/classes/Forms.php */