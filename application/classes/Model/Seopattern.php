<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Seopattern extends ORM {

	protected $_table_name = 'seo_patterns';
	
	public function rules()
	{
		return array(
			'category_id' => array(array('digit'), array('not_empty'),),
			'h1' => array(array('not_empty'),),
			'title' => array(array('not_empty'),),
			'description' => array(array('not_empty'),),
			'footer' => array(array('not_empty'),),
			'anchor' => array(array('not_empty'),),
		);
	}

	public function filters()
	{
		return array(
			'category_id' => array(array('trim'),),
			'h1' => array(array('trim'),),
			'title' => array(array('trim'),),
			'description' => array(array('trim'),),
			'footer' => array(array('trim'),),
			'anchor' => array(array('trim'),),
		);
	}	
	
	public function with_categories()
	{
		return $this->select(array( "category.title",  "category_title"))
					->join("category", "left")
						->on("seopattern.category_id", "=", "category.id");
	}
	
	

} // End Service_Invoices Model