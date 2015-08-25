<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute_Element extends ORM
{
	protected $_table_name = 'attribute_element';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'attribute_obj' => array('model' => 'Attribute', 'foreign_key' => 'attribute'),
	);

	public function by_value_and_attribute($value, $seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'attribute_element.attribute')
					->where("attribute_element.title","=",$value)
					->where("attribute.seo_name","=",$seo_name);
	}

	public function by_attribute_seoname($seo_name)
	{
		return $this->join('attribute')
					->on('attribute.id', '=', 'attribute_element.attribute')
					->where("attribute.seo_name","=",$seo_name);
	}

	public function get_elements_with_published_objects($category_id, $city_id = FALSE)
	{
		$object_subquery =  DB::select("value")
								   ->from("data_list")
								   ->join("object")
									   ->on("data_list.object","=","object.id") 
								   ->where("active","=","1")
								   ->where("is_published","=","1")
								   ->where("category","=", (int) $category_id);
		if ($city_id) {
			$object_subquery = $object_subquery->where("city_id","=", $city_id);
		}

		return ORM::factory('Attribute_Element')
					->select("attribute_element.*", array("attribute.seo_name","attribute_seo_name"))
					->join('attribute')
						->on("attribute_element.attribute","=","attribute.id")
					->join('reference')
						->on("attribute.id","=","reference.attribute")
					->where("attribute_element.id", "IN", $object_subquery)
					->where("reference.category", "=", (int) $category_id )
					->where("reference.is_seo_used","=",1);
	}	
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Attribute/Element.php */