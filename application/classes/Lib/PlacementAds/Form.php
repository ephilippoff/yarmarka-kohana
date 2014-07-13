<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_Form  {

	public $_category_id = 0;
	public $_object_id = 0;
	public $_city_id = 0;

	public $category_id = 0;
	public $object_id = 0;
	public $city_id = 0;
	public $user_id = 0;

	public $_edit = FALSE;
	public $_data;
	public $map = FALSE;

	private $category;
	private $object;
	private $city;

	const MAX_COUNT_CONTACTS = 3;

	public $templates = array(
		'category' 	=> 'add/block/category',
		'city' 		=> 'add/block/city',
		'subject' 	=> 'add/block/subject',
		'text' 		=> 'add/block/text',
		'photo' 	=> 'add/block/photo',
		'params' 	=> 'add/block/params',
		'map' 		=> 'add/block/map',
		'contacts' 	=> 'add/block/contacts',
	);

	function Lib_PlacementAds_Form($params)
	{
		$this->_data = new stdClass();

		if (in_array("category_id", $params)) 
			$this->_category_id = $params['category_id'];

		if (in_array("object_id", $params)) 
			$this->_object_id 	= $params['object_id'];

		if (in_array("city_id", $params)) 
			$this->_city_id 	= $params['city_id'];

		$this -> Get_Instances();
	}

	function Get_Instances()
	{
		$category_id 	= $this->_category_id;
		$city_id 		= $this->_city_id;
		$object_id 		= $this->_object_id;
		$user_id 		= Auth::instance()->get_user()->id;

		$this->object 	= ORM::factory('Object');
		$this->category = ORM::factory('Category');
		$this->city 	= ORM::factory('City');
		$this->user 	= ORM::factory('User');

		if ($object_id > 0) 
		{
			$this->object = ORM::factory('Object', $object_id); 

			if ($this->object->loaded())
			{				
				$category_id 		= $this->object->category;
				$city_id 	 		= $this->object->city_id;				
				$user_id		  	= $this->object->author;

				$this->_edit 		= TRUE;
				$this->object_id  	= $object_id;
			}
						
		}

		if ($category_id > 0)
		{
			$this->category = ORM::factory('Category', $category_id);
			if ($this->category->loaded())
			{
				$this->category_id = $category_id;
			} else {
				throw new Exception("Такой рубрики не существует");
			}
		}

		if ($city_id > 0)
		{
			$this->city = ORM::factory('City', $city_id);
			if ($this->city->loaded())
			{
				$this->city_id = $city_id;
			} else {
				throw new Exception("В нашей базе такого города не существует");
			}
		}

		if ($user_id > 0)
		{
			$this->user = ORM::factory('User', $user_id);
			if ($this->user->loaded())
			{
				$this->user_id = $user_id;
			} else {
				throw new Exception("Пользователя не существует");
			}
		}

		return $this;
	}

	function Category()
	{		
		$object 		= $this->object;
		$category 		= $this->category;
		$category_id 	= $this->category_id;
		$edit 			= $this->_edit;

		$category_list = ORM::factory('Category')
								->find_all();

		$value = $category->title;
		
		$this->_data->category = View::factory($this->templates['category'],
									array(	
											'category_list' => $category_list, 
											'category_id' 	=> $category_id,
											'value' 		=> $value,
											'edit'			=> $edit
										))
									->render();
		return $this;
	}

	function City()
	{
		$object 		= $this->object;
		$city 			= $this->city;
		$city_id 		= $this->city_id;
		$edit 		= $this->_edit;

		$city_list = ORM::factory('City')
							->where('is_visible','>',0)
							->find_all();

		$value = $city->title;
		
		$this->_data->city = View::factory($this->templates['city'],
									array(	'city_list' => $city_list, 
											'city_id' => $city_id,
											'value' => $value, 
											'edit' => $edit))
									->render();
		return $this;
	}

	function Other_Cities()
	{

		return $this;
	}

	function Params()
	{
		$category_id 	= $this->category_id;
		$object_id 		= $this->object_id;

		if (empty($category_id))
			return $this;

		$ar = ORM::factory('Attribute_Relation', $category_id);
		$params = Array();

		if ($object_id>0)
		{
			$values = ORM::factory('Data_List')->where("object","=",$object_id)->find_all();
			foreach($values as $list_item)
			{
				$params[$list_item->reference] = "_".$list_item->value;
			}

			$values = ORM::factory('Data_Integer')->where("object","=",$object_id)->find_all();
			foreach($values as $integer_item)
			{
				$params[$integer_item->reference] = $integer_item->value_min;
			}

			$values = ORM::factory('Data_Numeric')->where("object","=",$object_id)->find_all();
			foreach($values as $numeric_item)
			{
				$params[$numeric_item->reference] = $numeric_item->value_min;
			}

			$values = ORM::factory('Data_Text')->where("object","=",$object_id)->find_all();
			foreach($values as $text_item)
			{
				$params[$text_item->reference] = $text_item->value;
			}
		}

		$data = Attribute::getData($category_id);

		if (count($data[$category_id]) == 1)
			return $this;	
	
		//$params = Array("23" => "_223", "433" => "_1435", "466" => "_3238");

		$elements = Attribute::parseAttributeLevel($data[$category_id], $params);

		$customs = Array();
		foreach($elements as $key => $element)
		{			
			if ($element["custom"] == "address")
				$this->map = TRUE;

			if ($element["custom"] and substr($element["custom"], 0, 2) <> "i_") {
				$customs[] = $element;
				unset($elements[$key]);
			}			
		}

		$this->_data->params = View::factory($this->templates['params'],
									array('elements' => $elements, 'customs' => $customs))
									->render();
		
		return $this;
	}

	function Map()
	{
		if ($this->map)
			$this->_data->map = View::factory($this->templates['map'],
									array())
									->render();
		return $this;
	}

	function Subject()
	{
		$category 	= $this->category;
		$object 	= $this->object;
		$edit 		= $this->_edit;

		$title_auto_fill 	=  FALSE;
		$value 				= '';

		if ($object->loaded())
			$value = $object->title;

		if ($category->loaded())
			$title_auto_fill = $category->title_auto_fill;


		if (!$title_auto_fill OR ($title_auto_fill AND $edit))
			$this->_data->subject = View::factory($this->templates['subject'],
									array( 'value' => $value, 
											'edit' => $edit))->render();

		return $this;
	}

	function Text(){
		$object 	= $this->object;

		$value = '';
		if ($object->loaded())
			$value = $object->user_text;

		$this->_data->text = View::factory($this->templates['text'],
									array( 'value' => $value ))->render();

		return $this;
	}

	function Photo(){
		$object_id  = $this->object_id;
		$object 	= $this->object;

		$main_image_id = NULL;

		$files = Array();
		if ($object_id >0)
		{
			$oa = ORM::factory('Object_Attachment')->where("object_id","=",$object_id)->find_all();
			foreach($oa as $photo)
			{
				$filename = $photo->filename;
				$filepath = Imageci::getSitePaths($filename);
				$files[] = Array(
						'id' 		=> $photo->id,
						'filename'  => $filename,
						'filepath'  => $filepath["120x90"] 
					);
			}

			$main_image_id = $object->main_image_id;
		}

		$this->_data->photo = View::factory($this->templates['photo'],
									array( 'files' => $files,  
											'main_image_id' => $main_image_id
											))->render();

		return $this;
	}

	function Video(){

		return $this;
	}

	function Contacts(){
		$object_id  = $this->object_id;
		$object 	= $this->object;
		$contact_person = "";
		$oc = Array();
		$ct = ORM::factory('Contact_Type')->find_all();

		if ($object_id >0)
		{
			$oc = ORM::factory('Object_Contacts')->where("object_id","=",$object_id)->find_all();
			$contact_person = $object->contact;
		}

		$this->_data->contacts = View::factory($this->templates['contacts'],
									array(	"contacts" 			=> $oc, 
											"contact_types" 	=> $ct, 
											"max_count_contacts"=> self::MAX_COUNT_CONTACTS,
											"contact_person" 	=> $contact_person)
									)->render();
		return $this;
	}

	function Options(){

		return $this;
	}
}