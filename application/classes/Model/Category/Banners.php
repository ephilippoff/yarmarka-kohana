<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Category_Banners extends ORM {

	protected $_table_name = 'category_banners';
	
	public function rules()
	{
		return array(
			'cities' => array(
				array('not_empty'),
			),
			'category_id' => array(
				array('not_empty')
			),			
		);
	}	
	
	public function labels()
	{
		return array(
			'cities'         => '"Города"',
			'category_id'    => '"Рубрика"',

		);
	}
	
	public function filters()
	{
		return array(
			'href' => array(
				array('trim')
			)
		);
	}		
		
	protected $_belongs_to = array(
		'category'	=> array('model' => 'Category', 'foreign_key' => 'category_id'),
		'attr_element'	=> array('model' => 'Attribute_Element', 'foreign_key' => 'category_id'),
	);	

	function increase_visits($id)
	{
		if (!$id = (int)$id) return 0;
		
		$banner = ORM::factory('Category_Banners', $id);
		
		if ($banner->loaded())
		{
			$banner->visits++;
			$banner->save();
		}
							
		return;				
	}	
	
}
