<?php defined('SYSPATH') or die('No direct script access.');

class Form_Add  {

	public $_category_id = 0;
	public $_object_id = 0;
	public $_city_id = 0;

	public $category_id = 0;
	public $object_id = 0;
	public $city_id = 0;

	public $_edit = FALSE;
	public $_data;
	public $map = FALSE;

	private $category;
	private $object;
	private $city;

	const MAX_COUNT_CONTACTS = 3;

	function Form_Add($params, $is_post = FALSE, $errors = NULL)
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

		$this->object 	= ORM::factory('Object');
		$this->category = ORM::factory('Category');
		$this->city 	= ORM::factory('City');

		if ($object_id > 0) 
		{
			$this->object = ORM::factory('Object', $object_id); 

			if ($this->object->loaded())
			{				
				$category_id 		= $this->object->category;
				$city_id 	 		= $this->object->city_id;				

				$this->_edit 		= TRUE;
				$this->_data->_edit = $this->_edit;
				$this->object_id  	= $object_id;
			}
						
		}

		if ($category_id > 0)
		{

			$this->category = ORM::factory('Category', $category_id)->cached(DATE::WEEK);
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

		return $this;
	}

	function Login()
	{
		$errors 		= $this->errors;

		$this->_data->login = array("login_error" => $errors->not_autorized,
										"pass_error" => $errors->pass_error);

		return $this;
	}

	function Category()
	{		
		$object 		= $this->object;
		$category 		= $this->category;
		$category_id 	= $this->category_id;
		$edit 			= $this->_edit;
		$errors 		= $this->errors;

		$category_array = array();
		$category_list = ORM::factory('Category')
								->where("is_ready", "=", 1)
								->order_by("through_weight")
								->cached(DATE::WEEK)
								->find_all();
		foreach ($category_list as $item) {
			
			$childs = ORM::factory('Category')
				->where("parent_id","=",$item->id)
				->where("is_ready", "=", 1)
				->order_by("weight")
				->cached(DATE::WEEK)
				->find_all();
			if (count($childs)>0 AND $item->id <> 1)
			{
				$childs_array = array();
				foreach ($childs as $child) {
					if (!ORM::factory('Category')
							->where("parent_id","=",$child->id)
							->where("is_ready", "=", 1)
							->cached(DATE::WEEK)
							->count_all())
					{
						$childs_array[$child->id] = $child->title;
					}
				}

				$category_array[$item->title] = $childs_array;
			}
		}

		$value = $category->title;

		$default_action = ORM::factory('Category')->get_default_action($category_id);
		
		$this->_data->category = array(	
										'category_list' => $category_array, 
										'category_id' 	=> $category_id,
										'value' 		=> $value,
										'edit'			=> $edit,
										'default_action'=> $default_action,
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

		$city_array = array();
		$main_cities = array();
		$other_cities = array();
		$city_list = ORM::factory('City')
							->where('is_visible','>',0)
							->cached(DATE::WEEK)
							->find_all();
		foreach ($city_list as $city_item)
		{
			if (in_array($city_item->id, array(1979,1919,1948,1947)))
				$main_cities[$city_item->id] = $city_item->title;
			else 
				$other_cities[$city_item->id] = $city_item->title;
		}

		$city_array["Города"] 		 = $main_cities;
		$city_array["Другие"] = $other_cities;

		$value = $city->title;
		
		$this->_data->city = array(	'city_list' => $city_array, 
									'city_id' => $city_id,
									'value' => $value, 
									'edit' => $edit,
									'city_error' => $errors->city_kladr_id
									);
		return $this;
	}

	function AdvertType()
	{	
		$object 		= $this->object;
		/*<option <?php if ($type_tr == 88) : ?> selected <?php endif;?> value="88">Модульная реклама (88)</option>
										<option <?php if ($type_tr == 89) : ?> selected <?php endif;?> value="89">Рекламное объявление (89)</option>
										<option <?php if ($type_tr == 90) : ?> selected <?php endif;?> value="90">Рекламное объявление с фоном (90)</option>	
										*/
		$type = array(
				0 => "---",
				88 => "Модульная реклама (88)",
				89 => "Рекламное объявление (89)",
				90 => "Рекламное объявление с фоном (90)"
			);

		$value = $object->type_tr;
		if ( array_key_exists("obj_type", $this->params))
			$value = $this->params['obj_type'];

		$this->_data->advert_type = array(
											'type_list' => $type,
											'value'		=> $value
										);
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

		$ar = ORM::factory('Attribute_Relation', $category_id)->cached(DATE::WEEK);
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

			if ($element["custom"] == 'multiple')
			{
				$rows[] = $element;
				continue;
			}

			if ($element["custom"] and substr($element["custom"], 0, 2) <> "i_") {
				$rows[] = $element;
				continue;
			}	

			if (!$element["custom"] AND
					($element["type"] == "list" OR $element["type"] == "ilist"))
			{
				$lists[] = $element;
				continue;
			}

			if (!$element["custom"] AND
					$element["type"] <> "list" AND $element["type"] <> "ilist")
			{
				$rows[] = $element;
				continue;
			}		
		}

		$lists = self::sort_params($lists);
		$rows  = self::sort_params($rows);

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
		$errors 	= $this->errors;

		$title_auto_fill 	=  FALSE;
		$value 				= '';

		if ($object->loaded() AND !$this->is_post)
			$value = $object->title;
		elseif ($this->is_post AND array_key_exists("title_adv", $this->params))
			$value = $this->params['title_adv'];

		if ($category->loaded())
			$title_auto_fill = $category->title_auto_fill;


		if ((!$title_auto_fill AND !$edit) OR (!$title_auto_fill AND $edit))
			$this->_data->subject = array( 'value' => $value, 
											'edit' => $edit,
											'title_auto_fill' => $title_auto_fill,
											'subject_error' => $errors->title_adv);

		return $this;
	}

	function Text(){
		$category 	= $this->category;
		$object 	= $this->object;
		$errors 	= $this->errors;

		$value = '';
		if ($object->loaded() AND !$this->is_post)
			$value = $object->user_text;
		elseif ($this->is_post AND array_key_exists("user_text_adv", $this->params))
			$value = $this->params['user_text_adv'];

		$text_required = 1;
		if ($category->loaded())
			$text_required = $category->text_required;

		
		$this->_data->text = array( 'value' 		=> $value,
									'text_required' => $text_required,
									'text_error' 	=> $errors->user_text_adv );

		return $this;
	}

	function Photo(){
		$object_id  = $this->object_id;
		$object 	= $this->object;

		$main_image_id = NULL;

		$files = Array();

		if ($object->loaded() AND !$this->is_post)
		{
			$oa = ORM::factory('Object_Attachment')
					->where("object_id","=",$object_id)
					->order_by("id")
					->find_all();
			foreach($oa as $photo)
			{
				$filename = $photo->filename;
				$filepath = Imageci::getSitePaths($filename);
				$files[] = Array(
						'id' 		=> $photo->id,
						'filename'  => $filename,
						'filepath'  => $filepath["120x90"],
						'active'	=> ($object->main_image_id == $photo->id) ? TRUE : FALSE
					);
			}

			$main_image_id = $object->main_image_id;
		} elseif ($this->is_post AND array_key_exists("userfile", $this->params)
						AND count($this->params["userfile"]) > 0){
			$i = 0;
			foreach ($this->params["userfile"] as $photo)
			{
				$filename = $photo;
				$filepath = Imageci::getSitePaths($filename);
				$files[] = Array(
						'id' 		=> $i,
						'filename'  => $filename,
						'filepath'  => $filepath["120x90"],
						'active'	=> ($this->params["active_userfile"] == $filename) ? TRUE : FALSE
					);
				$i++;
			}
			if (array_key_exists("active_userfile", $this->params))
				$main_image_id = $this->params["active_userfile"];
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
		$contact_types = ORM::factory('Contact_Type')->cached(DATE::WEEK)->find_all();

		$user_id = NULL;
		if ($user = Auth::instance()->get_user())
			$user_id = $user->id;

		if ($object->loaded() AND !$this->is_post)
		{	
			self::parse_object_contact($object_id, $user_id, function($id, $value, $type, $verified) use (&$contacts){
				$contacts[] = Array("id" => $id, "type"  => $type,"value" => $value, "verified" => $verified);
			});
			$contact_person = $object->contact;
		}
		elseif ($this->is_post)
		{			
			self::parse_post_contact($this->params, $user_id, function($id, $value, $type, $verified) use (&$contacts){
				$contacts[] = Array("id" => $id, "type"  => $type,"value" => $value, "verified" => $verified);
			});

			if (!count($contacts))
				$contacts[] = Array("id" => "000", "type"  => 1,"value" => "", "verified" => false);

			$contact_person = $this->params["contact"];
		} elseif ($user_id)
		{
			self::parse_user_contact($user_id, function($id, $value, $type, $verified) use (&$contacts){
				$contacts[] = Array("id" => $id, "type"  => $type,"value" => $value, "verified" => $verified);
			});
			$contact_person = $user->fullname;
		} else
		{
			$contacts[] = Array("id" => "000", "type"  => 1,"value" => "", "verified" => false);
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
		$list_array = Array();
		foreach($values as $list_item)
		{
			if (!array_key_exists($list_item->reference, $params))
				$params[$list_item->reference] = array();

			$params[$list_item->reference][] = "_".$list_item->value;
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

	static private function parse_user_contact($user_id, $callback)
	{
		$oc = ORM::factory('User_Contact')
					->join("contacts","left")
						->on("contact_id","=","contacts.id")
					->where("contacts.verified_user_id","=",$user_id)
					->where("contacts.show","=",1)
					->where("user_id","=",$user_id)
					->find_all();
		foreach($oc as $contact)
		{
			$callback($contact->id, $contact->contact->contact, $contact->contact->contact_type_id, TRUE);
		}
	}

	static private function parse_object_contact($object_id, $user_id, $callback)
	{
		$oc = ORM::factory('Object_Contacts')->where("object_id","=",$object_id)->find_all();
		foreach($oc as $contact)
		{
			$verified = FALSE;
			if ($user_id)
			{
				$con = ORM::factory('Contact')
						->where("verified_user_id","=",$user_id)
						->where("contact_clear","=",$contact->contact->contact_clear)
						->find();
				if ($con->loaded())
					$verified = TRUE;
			}
			$callback($contact->id, $contact->contact->contact, $contact->contact->contact_type_id, $verified);
		}
	}

	static private function parse_post_contact($params, $user_id, $callback)
	{
		foreach((array) $params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$id = $matches[1];
				$value = trim($params['contact_'.$matches[1].'_value']);
				$type = $params['contact_'.$matches[1].'_type'];

				$verified = FALSE;
				if ($user_id)
				{
					if ($type <> "5")
						$value = Text::clear_phone_number($value);

					$con = ORM::factory('Contact')
							->where("verified_user_id","=", $user_id)
							->where("contact_clear","=", $value)
							->find();
					if ($con->loaded())
						$verified = TRUE;
				}

				$callback($id, $value, $type, $verified);
			}
		}
	}

	static private function sort_params($list)
	{
		uasort($list, function ($a, $b){
			if ($a == $b) {
		        return 0;
		    }
		    return ($a["weight"] < $b["weight"]) ? -1 : 1;
		});

		return $list;
	}
}