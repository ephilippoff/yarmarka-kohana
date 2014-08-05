<?php defined('SYSPATH') OR die('No direct script access.');

class Object_Utils
{
	const ATTRIBUTE_PRICE_ID = 44;

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

	public static function get_parsed_parameters($params = Array(), $object_id = 0, $ignore_price = FALSE)
	{
		$values 		= Array();
		$list_ids 		= Array();

		if ($object_id > 0)
		{
			$object = ORM::factory('Object', $object_id);
			$params = self::generate_form_element_by_object((int) $object_id);			
		} 

		$attributes = self::prepare_form_elements((array) $params);
		
		@list($values, $list_ids) 
					= self::parse_form_elements((array) $attributes, $ignore_price);

		if ($object_id > 0)
		{
			$values[] = ORM::factory('Location', $object->location_id)->address;
			$values[] = ORM::factory('City', 	 $object->city_id)->title;
		} else {
			$values[] = $params->address;
			$values[] = ORM::factory('City', $params->city_id)->title;
		}
		

		return Array($values, $list_ids);
	}

	public static function generate_form_element_by_object($object_id)
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

	/*
	*	$form_elements like ( param_<reference> => <value>, param_<reference>_min => <value_min>, param_<reference>_max => <value_max> ... etc)
	*	$ignore_price for generate signature without price
	*
	*	return Array
	*
	*/
	public static function parse_form_elements($form_elements = Array(), $ignore_price = FALSE)
	{
		$values 		= Array();
		$list_ids 		= Array();

		foreach ($form_elements as $reference_id => $value)
		{
			if (!$value) continue;
			$ref = ORM::factory('Reference')
						->select("attribute.type", Array("attribute.id", "aid"))
						->join('attribute', 'left')
						->on('reference.attribute', '=', 'attribute.id')
						->where('reference.id','=',$reference_id)
						->cached(Date::DAY)
						->find();
			
			//не включаем цену в подпись
			if ($ignore_price AND $ref->aid == self::ATTRIBUTE_PRICE_ID)
				continue;

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

						foreach ($el as $item)
							$list_ids[] = $item->id;

						foreach ($el as $item)
							$attribute_ids[] = $item->attribute;

						$values[] = join(", ", $eltitles);
					} else {
						$ae = ORM::factory('Attribute_Element', $value);
						$values[] 		 			= $ae->title;
						$list_ids[$ae->attribute] 	 = $ae->id;
					}
				break;
				default:
					if (is_array($value))
					{
						$values[] =$value["min"]."-".$value["max"];
						$list_ids[$ae->attribute] = $value["min"];
						$list_ids[$ae->attribute] = $value["max"];
					}
					else 
					{
						$values[] = $value;
						$list_ids[$ae->attribute] = $value;
					}

				break;
			}
		}


		return Array ($values, $list_ids);
	}

	public static function prepare_form_elements($params)
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

/* End of file Utils.php */
/* Location: ./application/classes/Object/Utils.php */