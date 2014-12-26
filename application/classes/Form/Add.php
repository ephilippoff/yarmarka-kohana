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

	function __construct($params, $is_post = FALSE, $errors = NULL)
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

			$this->category = ORM::factory('Category')
									->where("id","=",$category_id)
									->cached(DATE::WEEK, array("category", "add"))
									->find();
			if ($this->category->loaded())
			{
				$this->category_id = $category_id;
			} else {
				throw new Exception("Такой рубрики не существует");
			}
		}

		if ($this->_edit OR $this->is_post)		
			$this->city = ORM::factory('City', $city_id)->cached(DATE::WEEK, array("city", "add"));
		elseif (!$this->is_post AND isset($_COOKIE["location_city_id"]) AND $_COOKIE["location_city_id"])
			$this->city = ORM::factory('City', $_COOKIE["location_city_id"])->cached(DATE::WEEK, array("city", "add"));
		
		if ($this->city->loaded())
			$this->city_id = $this->city->id;

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
								->cached(DATE::WEEK, array("category", "add"))
								->find_all();
		foreach ($category_list as $item) {
			
			$childs = ORM::factory('Category')
				->where("parent_id","=",$item->id)
				->where("is_ready", "=", 1)
				->order_by("weight")
				->cached(DATE::WEEK, array("category", "add"))
				->find_all();
			if (count($childs)>0 AND $item->id <> 1)
			{
				$childs_array = array();
				foreach ($childs as $child) {
					if (!ORM::factory('Category')
							->where("parent_id","=",$child->id)
							->where("is_ready", "=", 1)
							->count_all(NULL, DATE::WEEK))
					{
						$childs_array[$child->id] = $child->title;
					}
				}

				$category_array[$item->title] = $childs_array;
			}
		}

		$category_array["Другие"] = array(
			156 => "В хорошие руки"
		);

		if ($user = Auth::instance()->get_user())
			if ($user->role == 9 OR $user->role == 1)
				$category_array["Другие"][155] = "Каталог компаний";

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
		$params  		= new Obj($this->params);

		$city_array = array();
		$main_cities = array();
		$other_cities = array();
		$city_list = ORM::factory('City')
							->where('is_visible','>',0)
							->cached(DATE::WEEK, array("city", "add"))
							->find_all();
		foreach ($city_list as $city_item)
		{
			$lat = $lon = "";
			$location = ORM::factory('Location')
						->where("id","=",$city_item->location_id)
						->cached(DATE::WEEK, array("city", "add"))
						->find();
			if ($location->loaded())
			{
				$lat = $location->lat;
				$lon = $location->lon;
			}

			if (in_array($city_item->id, array(1979,1919,1948,1947)))
				$main_cities[$city_item->id] = array(
									"title" => $city_item->title,
									"lat" => $lat,
									"lon" => $lon
								);
			else 
				$other_cities[$city_item->id] = array(
									"title" => $city_item->title,
									"lat" => $lat,
									"lon" => $lon
								);
		}

		$city_array["Города"] = $main_cities;
		$city_array["Другие"] = $other_cities;

		$city_title = NULL;
		if ($city->loaded())
			$city_title = $city->title;

		$lat = $lon = "";
		$location = ORM::factory('Location')
						->where("id","=",$city->location_id)
						->cached(DATE::WEEK, array("city", "add"))
						->find();
		if ($location->loaded())
		{
			$lat = $location->lat;
			$lon = $location->lon;
		}

		$real_city = NULL;
		$real_city_exists = FALSE;
		if ($this->is_post) {
			if ($params->real_city)
				$real_city = trim($params->real_city);

			$real_city_exists =  ($params->real_city_exists == "on") ? TRUE : FALSE;
		} elseif ($object->loaded()) 
		{
			$compiled_query = ORM::factory('Object_Compiled')
								->where("object_id","=",$object->id)
								->find();
			if ($compiled_query->loaded())
			{
				$compiled = unserialize($compiled_query->compiled);
				$real_city = (isset($compiled["real_city"])) ? $compiled["real_city"] : NULL;
				$real_city_exists = TRUE;
			}
		}

		$this->_data->city = array(	'city_list' => $city_array, 
									'city_title' => $city_title,
									'city_id' => $city_id,
									'real_city' => $real_city, 
									'real_city_exists' => $real_city_exists,
									'edit' => $edit,
									'city_error' => $errors->city_kladr_id,
									"lat" => $lat,
									"lon" => $lon
								);
		return $this;
	}

	function OrgInfo()
	{
		$user = Auth::instance()->get_user();
		$title = $user->org_name;
		if (!$title)
			$title = "Не указано название компании";

		$about = $user->about;
		if (!$about)
			$about = "Информация о компании не заполнена";
		else
			$about = substr($about, 0,300);

		$this->_data->org_info = array(
											'title' => $title,
											'logo'		=> $user->filename,
											'about'		=> $about
										);
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

	function CompanyInfo()
	{	
		$object 		= $this->object;
		
		$service_field_inn = $service_field_orgname = $service_field_description = NULL;

		if (!$this->is_post)
		{
			$compiled_query = ORM::factory('Object_Compiled')
							->where("object_id","=",$object->id)
							->find();
			if ($compiled_query->loaded())
			{
				$compiled = unserialize($compiled_query->compiled);
				$compiled = $compiled['service_fields'];
				$service_field_inn = $compiled["service_field_inn"];
				$service_field_orgname = $compiled["service_field_orgname"];
				$service_field_description = $compiled["service_field_description"];
			}
		} else {
			if ( array_key_exists("service_field_inn", $this->params))
				$service_field_inn = $this->params["service_field_inn"];
			if ( array_key_exists("service_field_orgname", $this->params))
				$service_field_orgname = $this->params["service_field_orgname"];
			if ( array_key_exists("service_field_description", $this->params))
				$service_field_description = $this->params["service_field_description"];
		}

		$info = array(
				"ИНН" => Form::input("service_field_inn", $service_field_inn, array("class" => "")),
				"Компания" => Form::input("service_field_orgname", $service_field_orgname, array("class" => "")),
				"Описание" => Form::textarea("service_field_description", $service_field_description, array("class" => ""))
			);

		$this->_data->company_info = array(
											'info' => $info
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


		$params = Array();

		if ($object->loaded() AND !$this->is_post)
		{
			$params = self::parse_object_params($object_id);
			$address_reference_id = ORM::factory('Attribute_Relation')
										->where("category_id","=",$category_id)
										->where("custom","=","address")
										->cached(DATE::WEEK, array("category","relation","add"))
										->find()->reference_id;
			if ($address_reference_id)
			{
				$location = ORM::factory('Location',$object->location_id)->cached(DATE::WEEK, array("city", "add"));
				$params[$address_reference_id] = $location->address;
			}

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

		$data = array();

		$cachekey = 'staticdata_addform_'.$category_id;
		$cachedata = Cache::instance()->get($cachekey);
		if ($cachedata)
			$data = $cachedata;
		else {
			$data = Attribute::getData($category_id);
			Cache::instance()->set($cachekey, $data);
		}

		

		if (count($data[$category_id]) == 1)
			return $this;	
	
		//$params = Array("23" => "_223", "433" => "_1435", "466" => "_3238");

		$cachekey = 'staticdata_addform_params_'.sha1($category_id.print_r($params, TRUE));
		$cachedata = Cache::instance()->get($cachekey);
		if ($cachedata)
			$elements = $cachedata;
		else {
			$elements = Attribute::parseAttributeLevel($data[$category_id], $params);
			Cache::instance()->set($cachekey, $elements);
		}

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
		$object 	= $this->object;

		if ($this->map)
			$this->_data->map = array();

		if ($object->loaded() AND !$this->is_post)
		{
			$location = ORM::factory('Location',$object->location_id)->cached(DATE::WEEK, array("city", "add"));;
			$this->_data->object_coordinates = $location->lat.",".$location->lon;
		} elseif ($this->is_post) {
			$this->_data->object_coordinates = $this->params['object_coordinates'];
		}
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

		if ($value)
			$value = Text::clear_usertext_tags($value);

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
					->where("type","<>",2)
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
		$object_id  = $this->object_id;
		$object 	= $this->object;
		$errors 	= $this->errors;

		$value = NULL;
		$embed = '';
		if ($object->loaded() AND !$this->is_post)
		{
			$oa = ORM::factory('Object_Attachment')
					->where("object_id","=",$object_id)
					->where("type","=",2)
					->order_by("id")
					->find();
			if ($oa->loaded())
			{
				$value = "http://youtu.be/".$oa->filename;
				$embed = '<iframe src="http://www.youtube.com/embed/' . $oa->filename . '" type="text/html" width="400" height="300" frameborder="0" allowfullscreen></iframe>';
			}
		}
		elseif ($this->is_post AND array_key_exists("video", $this->params))
		{
			$value = $this->params['video'];

			$youtube = '@youtu(?:(?:\.be/([_\-A-Za-z0-9]+))|(?:be.com/(?:(?:watch\?v=)|(?:embed/))([\-A-Za-z0-9]+)))@i';
			$filename = '';
			$error = NULL;

			if ( preg_match($youtube, $value, $matches) ) {//youtube
				if ( !empty($matches[1]) ) {
					$filename = $matches[1];
				} else {
					$filename = $matches[2];
				}

				$embed = '<iframe src="http://www.youtube.com/embed/' . $filename . '" type="text/html" width="400" height="300" frameborder="0" allowfullscreen></iframe>';
			} /*else {
				$error = 'Неподдерживаемый видеохостинг';
			}*/
		}

		

		$this->_data->video = array( 
									 'value' => $value,  
								     'embed' => $embed,
								     'video_error' => $errors->video
									);
		return $this;
	}

	function Contacts(){
		$object_id  = $this->object_id;
		$object 	= $this->object;
		$errors		= $this->errors;
		$contact_person = "";
		$contacts = Array();
		$contact_types = ORM::factory('Contact_Type')->cached(DATE::WEEK, array("contact", "add"))->find_all();

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
			$phone_exists = FALSE;
			$email_exists = FALSE;
			self::parse_user_contact($user_id, function($id, $value, $type, $verified) use (&$contacts, &$phone_exists, &$email_exists){
				$contacts[] = Array("id" => $id, "type"  => $type,"value" => $value, "verified" => $verified);
				if ($type == 1 OR $type == 2) $phone_exists = TRUE;
				if ($type == 5) $email_exists = TRUE;
			});

			if (!$phone_exists)
				$contacts[] = Array("id" => "000", "type"  => 1,"value" => "", "verified" => false);

			if (!$email_exists)
				$contacts[] = Array("id" => "001", "type"  => 5,"value" => "", "verified" => false);

			$contact_person = $user->fullname;
		} else
		{
			$contacts[] = Array("id" => "000", "type"  => 1,"value" => "", "verified" => false);
			$contacts[] = Array("id" => "001", "type"  => 5,"value" => "", "verified" => false);
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

	function Price(){
		$object 	= $this->object;

		$user = Auth::instance()->get_user();
		if (!$user)
			return $this;

		$prices = ORM::factory('Priceload')
						->where("user_id","=",$user->id)
						->where("state","=",2)
						->find_all();

		$value = NULL;
		if ($object->loaded() AND !$this->is_post)
		{
			$value = ORM::factory('Object_Priceload')
						->where("object_id","=",$object->id)
						->find()->priceload_id;
		}
		elseif ($this->is_post and array_key_exists('pricelist', $this->params))
		{
			$value = $this->params['pricelist'];
		}

		$this->_data->price = array(
									 'prices' => $prices,
									 'value' => $value
									);
		return $this;
	}
	
	function Widgets()
	{		
		$this->_data->last_news = ORM::factory('Article')->get_lastnews_from_rubric('novosti-yarmarki');
		
		return $this;
	}	

	function LinkedUser()
	{
		
		$object 	= $this->object;
		$user = Auth::instance()->get_user();
		$value = "off";

		if ($this->is_post)
		{ 
			if (isset($this->params['link_to_company']))
				$value = $this->params['link_to_company'];
		} elseif ($this->_edit AND $object->loaded())
		{
			$value = ($object->author <> $object->author_company_id) ? "on" : "off";
		} else {
			$value = "on";
		}
		
		$this->_data->linked_company = array(
				"company" => ORM::factory('User', $user->linked_to_user),
				"value" => $value
			);
	}

	function Additional()
	{
		$category_id 	= $this->category_id;
		$errors 		= $this->errors;
		$user = Auth::instance()->get_user();

		$vakancy_org_type = Kohana::$config->load("dictionaries.vakancy_org_type");
		$fields = $values = $settings =  array();

		if ($category_id AND $user) 
		{
			
			$settings = Kohana::$config->load("category.".$category_id.".additional_fields.".$user->org_type);
			if (!$settings)
				$settings = array();
		}

		if ($this->is_post)
		{
			$fields = preg_grep("/^additional_/", array_keys( (array)$this->params ) );
			foreach ($fields as $field) {
				$values[$field] = $this->params[$field];
			}
		} else {
			if ($user)
			{
				$orginfo_data = ORM::factory('User_Settings')
								->get_group($user->id, "orginfo");
				foreach ($orginfo_data as $key => $data) {
					$values["additional_".$key] = $data;
				}
			}
		}
	

		$this->_data->additional = array(
						"errors" => $errors,
						"settings" => new Obj($settings),
						"values" =>  new Obj($values),
						"vakancy_org_type" => $vakancy_org_type
			);
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
					->where("user_id","=",$user_id)
					->limit(3)
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