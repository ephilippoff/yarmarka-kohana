<?php defined('SYSPATH') OR die('No direct script access.');

class Object_Utils
{

	public static function generate_text_for_signature($subject, $text, $params = Array())
	{
		return strip_tags($subject).', '.strip_tags($text).', '.join(', ', $params);
	}

	public static function generate_signature($text)
	{
		$result = array();
		$array = explode(' ',strip_tags($text));
		foreach ($array as $string)
		{
			$string = trim(Text::remove_symbols(trim($string)));
			if ($string)
			{
				$result[] = $string;
			}
		}
		return $result;
	}

	public static function get_values_from_form_elements($params)
	{
		$return = Array();
		$attributes = Object_Utils::get_form_elements_from_params((array) $params);
		foreach ($attributes as $reference_id => $value)
		{
			$ref = ORM::factory('Reference')
						->select("attribute.type")
						->join('attribute', 'left')
						->on('reference.attribute', '=', 'attribute.id')
						->where('reference.id','=',$reference_id)
						->cached(Date::DAY)
						->find();

			$type = $ref->type;

			switch($type){
				case 'list':
					//todo учесть массив
					$return[] = ORM::factory('Attribute_Element', $value)->title;
				break;
				default:
					$return[] =$value;
				break;
			}
		}
		$return[] = $params->address;
		$return[] = ORM::factory('City', $params->city_id)->title;

		return $return;
	}

	public static function get_form_elements_from_params($params)
	{
		$result = array();
		foreach ($params as $key => $value)
		{
			if (preg_match('/param_([0-9]*)[_]{0,1}(.*)/', $key, $matches))
			{
				$reference_id = $matches[1];
				$postfix = $matches[2]; // max/min

				if ($postfix)
				{
					$result[$reference_id][$postfix] = trim($value);
				}
				else
				{	//Если несколько значений(is_multiple)
					if (is_array($value))
						//Организовываем подмассив
						foreach ($value as $one_value) 
							$result[$reference_id][] = $one_value;					
					else
						$result[$reference_id] = trim($value);
				}
			}
		}
		
		return $result;
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Object.php */