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
			if ($string and strtolower($string) <> "null")
			{
				$result[] = $string;
			}
		}
		return $result;
	}

	public static function get_parsed_parameters($params = Array())
	{
		$values 		= Array();
		$list_ids 		= Array();

		$attributes = self::prepare_form_elements((array) $params);
		
		@list($values, $list_ids) = self::parse_form_elements((array) $attributes);

		$values[] = ORM::factory('City', (int) $params->city_id)->title;
		

		return Array($values, $list_ids);
	}

	/*
	*	$form_elements like ( param_<reference> => <value>, param_<reference>_min => <value_min>, param_<reference>_max => <value_max> ... etc)
	*
	*	return Array
	*
	*/
	public static function parse_form_elements($form_elements = Array())
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
						$value = (int) $value;
						if (!$value) continue;
						$ae = ORM::factory('Attribute_Element', $value);
						$values[] 		 			= $ae->title;
						$list_ids[$ae->attribute] 	 = $ae->id;
					}
				break;
				default:
					if (is_array($value))
					{
						$values[] =$value["min"]."-".$value["max"];
						$list_ids[$ref->aid] = $value["min"]."-".$value["max"];
					}
					else 
					{
						$values[] = $value;
						$list_ids[$ref->aid] = $value;
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