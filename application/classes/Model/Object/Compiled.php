<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Compiled extends ORM
{
	protected $_table_name = 'object_compiled_surgut';


	public function naming_attributes(Array $params)
	{

		$result = array();
		foreach ($params as $param) {
			$seo_name = $param["_attribute"]["seo_name"];
			$value = NULL;
			
			switch ($param["_attribute"]["type"]) {
				case 'list':
					$value = $param["_element"]["title"];
				break;
				case 'integer':
				case 'numeric':
					if ($param["value_min"] && $param["value_max"])
						$value = $param["value_min"]."-".$param["value_max"];
					elseif (!$param["value_min"] && $param["value_max"])
						$value = "до ".$param["value_max"];
					elseif ($param["value_min"] && !$param["value_max"])
						$value = $param["value_min"];
				break;
				default:
					$value = $param["value"];
				break;
			}

			$result[$seo_name] = $value;
		}
		return $result;
	}
}