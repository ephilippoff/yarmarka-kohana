<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_Form  {

	public $_category_id = 0;
	public $_object_id = 0;
	public $_city_id = 0;
	public $_edit = FALSE;
	public $_data;

	private $category;
	private $object;
	private $city;

	public $templates = array(
		'category' 	=> 'add/block/category',
		'city' 		=> 'add/block/city',
		'subject' 	=> 'add/block/subject',
		'text' 		=> 'add/block/text',
	);

	function Lib_PlacementAds_Form($params)
	{
		$this->_data = new stdClass();
		if (in_array("category_id", $params)) {
			$this->_category_id = $params['category_id'];
		}

		if (in_array("object_id", $params)) {
			$this->_object_id = $params['object_id'];
		}

		if (in_array("city_id", $params)) {
			$this->_city_id = $params['city_id'];
		}

		if ($this->_object_id > 0)
		{
			$this->_edit = TRUE;	
		}

		$this -> Get_Instances();
	}

	function Get_Instances()
	{
		$category_id 	= $this->_category_id;
		$city_id 		= $this->_city_id;
		$object_id 		= $this->_object_id;


		if ($category_id > 0)
		{
			$this->category = ORM::factory('Category', $category_id);
		}

		if ($city_id > 0)
		{
			$this->city = ORM::factory('City', $city_id);
		}

		if ($object_id > 0)
		{
			$this->object = ORM::factory('Object', $object_id);
		}
	}

	function Category(){
		$category_id 	= $this->_category_id;
		$edit 			= $this->_edit;

		$category_list = ORM::factory('Category')
								->find_all();
		
		$this->_data->category = View::factory($this->templates['category'],
									array(	
											'category_list' => $category_list, 
											'category_id' 	=> $category_id,
											'edit' 			=> $edit
										))
									->render();
		return $this;
	}

	function City(){
		$city_id 	= $this->_city_id;
		$edit 		= $this->_edit;

		$city_list = ORM::factory('City')
							->where('is_visible','>',0)
							->find_all();
		
		$this->_data->city = View::factory($this->templates['city'],
									array('city_list' => $city_list, 
											'city_id' => $city_id,
											'edit' => $edit))
									->render();
		return $this;
	}

	function Other_Cities(){

		return $this;
	}

	function Params(){

		return $this;
	}

	function Subject(){
		$category 	= $this->category;
		$object 	= $this->object;

		$title_auto_fill = NULL;
		if ($category->loaded())
		{
			$title_auto_fill = $category->title_auto_fill;
		}

		if (empty($title_auto_fill)) 
		{
			$this->_data->subject = View::factory($this->templates['subject'],
									array())->render();	
		} else {

			$this->_data->subject = "";

		}

		return $this;
	}

	function Text(){
		$object 	= $this->object;

		$this->_data->text = View::factory($this->templates['text'],
									array())->render();

		return $this;
	}

	function Photo(){

		return $this;
	}

	function Video(){

		return $this;
	}

	function Contacts(){

		return $this;
	}

	function Optionss(){

		return $this;
	}
}