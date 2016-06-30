<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Integer extends Data
{
	protected $_table_name = 'data_integer';

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function filters()
	{
		return array(
			'value_min' => array(
				array('intval'),
			),
			'value_max' => array(
				array('intval'),
			),
		);
	}

	public function by_value_and_attribute($value, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'data_integer.attribute')
					->where("data_integer.value_min","=",$value)
					//->where("data_integer.value_max","=",$value)
					->where("attribute.seo_name","=",$seo_name)
					->find();
	}

	public function by_object_and_attribute($object_id, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'data_integer.attribute')
					->where("data_integer.object","=",$object_id)
					->where("attribute.seo_name","IN", is_array($seo_name) ? $seo_name : array($seo_name))
					->find();
	}

	public function get_min_max_price($object_id)
	{
		$query = DB::select(DB::expr("MIN(value_min) as min, MAX(value_min) AS max"))
					->from($this->_table_name)
					->where("object", "=", $object_id)
					->where("attribute", "=", 44);
		$result = $query->execute();

		return Array (  (int) $result->get('min'),  (int) $result->get('max') );
	}

	public function get_compile()
	{
		if (!$this->loaded())
			return;

		$result = $this->as_array();

		$result["_attribute"] 	= $this->attribute_obj->select_array(array("id","title","seo_name","type"));
		$result["_type"] = "Integer";

		return $result;
	}

	public static function calculate_square_price($price, $square, $multiply = 1)
	{
		return round($price / ($square * $multiply), 1);
	}

	public function save_price_per_square($object_id, $category_id)
	{
		$square_attributes = array(
			"ploshchad",
			"ploshchad-2x2",
			"ploshchad-uchastka-v-sotkakh"
		);

		$price_attribute = 'tsena';

		if (!$object_id) return FALSE;

		$price_per_square_attribute = ORM::factory('Attribute')
			->select('attribute.id','attribute.seo_name', DB::expr('reference.id as reference_id'))
			->join('reference')
				->on('attribute.id','=','reference.attribute')
			->where('reference.category','=',$category_id)
			->where('seo_name','=','price-per-square')->cached(Date::WEEK)->find();

		if (!$price_per_square_attribute->loaded()) return FALSE;

		$_price = ORM::factory('Data_Integer')
						->by_object_and_attribute($object_id, $price_attribute);

		if (!$_price->loaded()) return FALSE;

		$_square = ORM::factory('Data_Numeric')
				->select('data_numeric.id','data_numeric.value_min', DB::expr('attribute.seo_name as seo_name'))
				->by_object_and_attribute($object_id, $square_attributes);

		if (!$_square->loaded()) {
			$_square = ORM::factory('Data_Integer')
				->select('data_integer.id','data_integer.value_min', DB::expr('attribute.seo_name as seo_name'))
				->by_object_and_attribute($object_id, $square_attributes);
		}

		if (!$_square->loaded()) return FALSE;

		

		if ($_square->value_min > 0 AND $_price->value_min > 0) {

			$item = ORM::factory('Data_Integer')->where('object','=',$object_id)->where('attribute','=',$price_per_square_attribute->id)->find();
			$item->attribute 	= $price_per_square_attribute->id;
			$item->object 		= $object_id;
			$item->reference 	= $price_per_square_attribute->reference_id;
			$item->value_min 	= Model_Data_Integer::calculate_square_price($_price->value_min, $_square->value_min, ($_square->seo_name == 'ploshchad-uchastka-v-sotkakh') ? 100 : 1) ;

			return $item->save();

		} else {

			return FALSE;

		}

		
	}
}

/* End of file Integer.php */
/* Location: ./application/classes/Model/Data/Integer.php */