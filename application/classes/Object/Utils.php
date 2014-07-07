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

	public static function get_all_values_of_object($params = Array(), $object_id = 0)
	{
		$return = Array();



		if ($object_id > 0)
		{
			$object = ORM::factory('Object', $object_id);
			$params = Object_Utils::get_values_from_object((int) $object_id);
			$attributes = Object_Utils::get_form_elements_from_params((array) $params);
		} else {
			$attributes = Object_Utils::get_form_elements_from_params((array) $params);
		}

		$return = Object_Utils::get_values_from_form_elements((array) $attributes);

		if ($object_id > 0)
		{
			$return[] = ORM::factory('Location', $object->location_id)->address;
			$return[] = ORM::factory('City', 	 $object->city_id)->title;
		} else {
			$return[] = $params->address;
			$return[] = ORM::factory('City', $params->city_id)->title;
		}
		

		return $return;
	}

	public static function get_values_from_object($object_id)
	{
		$data = Array();
		$return = Array();

		$object = ORM::factory('Object', $object_id) ;
		
		$dl = ORM::factory('Data_List')->select("reference.weight")
					->join('reference', 'left')
					->on('reference.id', '=', 'data_list.reference')
					->where('object','=',$object_id)
					->find_all();
		foreach ($dl as $item) 
			$data[$item->weight] = Array( "type" => "list", "id" => $item->reference, "value" => $item->value);


		$di = ORM::factory('Data_Integer')->select("reference.weight")
					->join('reference', 'left')
					->on('reference.id', '=', 'data_integer.reference')
					->where('object','=',$object_id)
					->find_all();
		foreach ($di as $item) 
			$data[$item->weight] = Array( "type" => "integer", "id" => $item->reference, "min" => $item->value_min, "max" => $item->value_max);
		
		$dn = ORM::factory('Data_Numeric')->select("reference.weight")
					->join('reference', 'left')
					->on('reference.id', '=', 'data_numeric.reference')
					->where('object','=',$object_id)
					->find_all();
		foreach ($dn as $item) 
			$data[$item->weight] = Array( "type" => "integer", "id" => $item->reference, "min" => $item->value_min, "max" => $item->value_max);


		$dt = ORM::factory('Data_Text')->select("reference.weight")
					->join('reference', 'left')
					->on('reference.id', '=', 'data_text.reference')
					->where('object','=',$object_id)
					->find_all();
		foreach ($dt as $item)
			$data[$item->weight] = Array( "type" => "list", "id" => $item->reference, "value" => $item->value);

		ksort($data);
		echo var_dump($data);

		foreach ($data as $item => $value)
		{
			if ($value["type"] == "list")
				$return["param_".$value["id"]] = $value["value"];
			else {
				$return["param_".$value["id"]] = $value["min"];
			}
		}
		return $return;
	}

	public static function get_values_from_form_elements($attributes = Array())
	{
		$return = Array();
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
					if (is_array($value))
					{
						$eltitles = Array();
						$el = ORM::factory('Attribute_Element')->where("id","IN",$value)->find_all();
						foreach ($el as $item)
							$eltitles[] = $item->title;

						$return[] = join(", ", $eltitles);
					} else {
						$return[] = ORM::factory('Attribute_Element', $value)->title;
					}
				break;
				default:
					if (is_array($value))
						$return[] =$value["min"]."-".$value["max"];
					else
						$return[] = $value;

				break;
			}
		}


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