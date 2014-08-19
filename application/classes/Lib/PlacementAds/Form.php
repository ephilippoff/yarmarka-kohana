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

	function Lib_PlacementAds_Form($params, $is_post = FALSE, $errors = NULL)
	{
		$this->_data = new Obj();
		$this->is_post = $is_post;
		$this->params  = $params;
		$this->error   = $errors;

		if ($errors)
		{
			$this->errors = new Obj($errors);
		}		

		if (array_key_exists("rubricid", $params)) 
			$this->_category_id = (int) $params['rubricid'];

		if (array_key_exists("object_id", $params)) 
			$this->_object_id 	= (int) $params['object_id'];

		if (array_key_exists("city_id", $params)) 
			$this->_city_id 	= (int) $params['city_id'];

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
		$errors 		= $this->errors;

		$category_list = ORM::factory('Category')
								->find_all();

		$value = $category->title;
		
		$this->_data->category = array(	
										'category_list' => $category_list, 
										'category_id' 	=> $category_id,
										'value' 		=> $value,
										'edit'			=> $edit,
										'category_error' => $errors->rubricid
									);

		return $this;
	}

	function City()
	{
		$object 		= $this->object;
		$city 			= $this->city;
		$city_id 		= $this->city_id;
		$edit 			= $this->_edit;
		$errors 		= $this->errors;

		$city_list = ORM::factory('City')
							->where('is_visible','>',0)
							->find_all();

		$value = $city->title;
		
		$this->_data->city = array(	'city_list' => $city_list, 
									'city_id' => $city_id,
									'value' => $value, 
									'edit' => $edit,
									'city_error' => $errors->city_kladr_id
									);
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
		$object 		= $this->object;
		$errors 		= $this->errors;

		if (empty($category_id))
			return $this;

		$ar = ORM::factory('Attribute_Relation', $category_id);
		$params = Array();

		if ($object->loaded() AND !$this->is_post)
		{
			$params = self::parse_object_params($object_id);

		} elseif ($this->is_post)
		{
			self::parse_post_params($this->params,  function($ref_id, $value, $minmax) use (&$params){
				$params[$ref_id] = $value;
			});
			/*foreach($this->params as $key=>$value)
			{
				$param = explode("_",$key);
				if (count($param) == 2)
				{
					$params[$param[1]] = $value;
				}
			}*/
		}

		$data = Attribute::getData($category_id);

		if (count($data[$category_id]) == 1)
			return $this;	
	
		//$params = Array("23" => "_223", "433" => "_1435", "466" => "_3238");

		$elements = Attribute::parseAttributeLevel($data[$category_id], $params);

		$lists = Array();
		$rows = Array();
		$customs = Array();
		foreach($elements as $key => $element)
		{			
			if ($element["custom"] == "address")
				$this->map = TRUE;

			if ($element["custom"] and substr($element["custom"], 0, 2) <> "i_") {
				$customs[] = $element;
				unset($elements[$key]);
			}	

			if (!$element["custom"] AND
					($element["type"] == "list" OR $element["type"] == "ilist"))
			{
				$lists[] = $element;
			}

			if (!$element["custom"] AND
					$element["type"] <> "list" AND $element["type"] <> "ilist")
			{
				$rows[] = $element;
			}		
		}

		$this->_data->params = array('elements' => $lists, 
										'rows'  => $rows,
											'customs' => $customs, 
												'errors' => $errors );
		
		return $this;
	}

	function Map()
	{
		if ($this->map)
			$this->_data->map = array();
		return $this;
	}

	function Subject()
	{
		$category 	= $this->category;
		$object 	= $this->object;
		$edit 		= $this->_edit;
		$errors 		= $this->errors;

		$title_auto_fill 	=  FALSE;
		$value 				= '';

		if ($object->loaded() AND !$this->is_post)
			$value = $object->title;
		elseif ($this->is_post AND array_key_exists("title_adv", $this->params))
			$value = $this->params['title_adv'];

		if ($category->loaded())
			$title_auto_fill = $category->title_auto_fill;


		if (!$title_auto_fill OR ($title_auto_fill AND $edit))
			$this->_data->subject = array( 'value' => $value, 
											'edit' => $edit,
											'subject_error' => $errors->title_adv);

		return $this;
	}

	function Text(){
		$object 	= $this->object;
		$errors 		= $this->errors;

		$value = '';
		if ($object->loaded() AND !$this->is_post)
			$value = $object->user_text;
		elseif ($this->is_post)
			$value = $this->params['user_text_adv'];

		$this->_data->text = array( 'value' => $value,
									'text_error' => $errors->user_text_adv);

		return $this;
	}

	function Photo(){
		$object_id  = $this->object_id;
		$object 	= $this->object;

		$main_image_id = NULL;

		$files = Array();

		if ($object->loaded() AND !$this->is_post)
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
		} elseif ($this->is_post){
			
		}

		$this->_data->photo = array( 'files' => $files,  
											'main_image_id' => $main_image_id
											);

		return $this;
	}

	function Video(){

		return $this;
	}

	function Contacts(){
		$object_id  = $this->object_id;
		$object 	= $this->object;
		$errors		= $this->errors;
		$contact_person = "";
		$contacts = Array();
		$contact_types = ORM::factory('Contact_Type')->find_all();

		if ($object->loaded() AND !$this->is_post)
		{	
			self::parse_object_contact($object_id, function($value, $type) use (&$contacts){
				$contacts[] = Array("type"  => $type,"value" => $value);
			});
			$contact_person = $object->contact;
		}
		elseif ($this->is_post)
		{			
			self::parse_post_contact($this->params, function($value, $type) use (&$contacts){
				$contacts[] = Array("type"  => $type,"value" => $value);
			});
			$contact_person = $this->params["contact"];
		}

		$this->_data->contacts = array(	"contacts" 			=> $contacts , 
										"contact_types" 	=> $contact_types, 
										"max_count_contacts"=> self::MAX_COUNT_CONTACTS,
										"contact_person" 	=> $contact_person,
										"contact_error" 	=> $errors->contact,
										"contacts_error" 	=> $errors->contacts);
		return $this;
	}
	

	function Options(){

		return $this;
	}

	static private function parse_object_params($object_id){
		$values = ORM::factory('Data_List')->where("object","=",$object_id)->find_all();
		$params = Array();
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

		return $params;
	}

	static private function parse_post_params($params, $callback)
	{
		foreach((array) $params as $key=>$value)
		{
			if (preg_match('/^param_([0-9]*)/', $key, $matches))
			{
				@list($tmp, $ref_id) = explode("_",$key);
				$callback($ref_id, $value, NULL);
			}
			if (preg_match('/^param_([0-9]*)_min/', $key, $matches))
			{
				@list($tmp, $ref_id, $tmp2) = explode("_",$key);
				$callback($ref_id, $value, $tmp2);
			}			
			if (preg_match('/^param_([0-9]*)_max/', $key, $matches))
			{
				@list($tmp, $ref_id, $tmp2) = explode("_",$key);
				$callback($ref_id, $value, $tmp2);
			}
		}
	}

	static private function parse_object_contact($object_id, $callback)
	{
		$oc = ORM::factory('Object_Contacts')->where("object_id","=",$object_id)->find_all();
		foreach($oc as $contact)
		{
			$callback($contact->contact->contact, $contact->contact->contact_type_id);
		}
	}

	static private function parse_post_contact($params, $callback){
		foreach((array) $params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$value = trim($params['contact_'.$matches[1].'_value']);
				$type = $params['contact_'.$matches[1].'_type'];

				$callback($value, $type);
			}
		}
	}
}