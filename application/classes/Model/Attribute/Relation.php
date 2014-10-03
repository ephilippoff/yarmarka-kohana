<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute_Relation extends ORM
{
	protected $_table_name = 'attribute_relation';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'attribute_element_obj' => array('model' => 'Attribute_Element', 'foreign_key' => 'parent_element_id'),
		'category_obj' => array('model' => 'Category', 'foreign_key' => 'category_id'),
		'parent_element_id' => array('model' => 'Attribute_Relation', 'foreign_key' => 'parent_id'),
		'reference_obj' => array('model' => 'Reference', 'foreign_key' => 'reference_id'),
		'parent_obj' => array('model' => 'Attribute_Relation', 'foreign_key' => 'parent_id'),
	);

	public function parse_options($options)
	{
		$count_options = count(explode(",", $options));
		if ($count_options>1)
		{
			try {

				$result = array();
				$params = explode(",", $options);
				foreach ($params as $param) {
					$keyvalue = explode("=", $param);
					$result[$keyvalue[0]] = $keyvalue[1];
					
				}
				return $result;
			}
			catch (Exception $e) 
			{
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function is_ilist($options)
	{
		if (!$this->parse_options($options))
			return FALSE;

		$options = new Obj($this->parse_options($options));

		if ($options->min AND $options->max AND $options->step)
			return TRUE;
		else
			return FALSE;
	}
}

/* End of file Element.php */
/* Location: ./application/classes/Model/Attribute/Element.php */