<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Attribute extends ORM
{
	protected $_table_name = 'attribute';

	public function rules()
	{
		return array(
			'title' => array(array('not_empty'),),
			'solid_size' => array(array('digit'),),
			'frac_size' => array(array('digit'),),
			'max_text_length' => array(array('digit'),),
			'is_textarea' => array(array('digit'),),
			'type' => array(array('not_empty'),),
			'is_prefix' => array(array('digit'),),
			'is_unit' => array(array('digit'),),
			'is_price' => array(array('digit'),),
			'is_descr' => array(array('digit'),),
			'seo_name' => array(array('not_empty'),),		
		);
	}	

	public function filters()
	{
		return array(
			'title' => array(array('trim'),),
			'solid_size' => array(array('intval'),),
			'frac_size' => array(array('intval'),),
			'max_text_length' => array(array('intval'),),
			'is_textarea' => array(array('intval'),),
			'type' => array(array('trim'),),
			'is_prefix' => array(array('intval'),),
			'is_unit' => array(array('intval'),),
			'is_price' => array(array('intval'),),
			'is_descr' => array(array('intval'),),
			'parent' => array(array(array($this, 'to_null'))),
			'seo_name' => array(array('trim'),),
			'comment' => array(array('trim'),),
		);
	}	
	
	public function to_null($value)
	{
		return !trim($value) ? null : trim($value);
	}	
	
	protected $_has_many = array(
	);

	protected $_belongs_to = array(
	);
}

/* End of file Attribute.php */
/* Location: ./application/classes/Model/Attribute.php */