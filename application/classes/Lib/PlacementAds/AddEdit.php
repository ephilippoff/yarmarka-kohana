<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEdit {
	public $user;
	public $user_id;
	public $category;
	public $errors;
	public $params;
	public $validation;
	public $contacts;
	public $city;
	public $city_id;
	public $location;
	public $signature = NULL;
	public $original_params;
	public $is_edit = NULL;

	public $parent_id;
	public $create_union = FALSE;
	public $edit_union = FALSE;
	public $destroy_union = FALSE;
	public $object_without_parent_id = NULL;
	public $category_settings = array();

	public $union_cancel = FALSE;

	function Lib_PlacementAds_AddEdit()
	{
		$this->init_defaults();
	}

	function init_input_params($params)
	{
		$this->original_params = $params;

		if (is_array($params))
		{

			$this->params = new Obj($params);
			$data_params = array();
			foreach((array) $this->params as $key=>$value){
				if (preg_match('/^param_([0-9]*)/', $key, $matches))
				{
					$this->params->{$key} = str_replace("_", "", $this->params->{$key});
					$data_params[] = explode("_", $key);
				}
			}

			if (!$this->params->address)
				$this->params->address = $this->parse_address_from_params((array) $this->params);

			

		}
		return $this;
	}

	function parse_address_from_params($params)
	{
		$address = "";
		$param_keys = array_keys($params);
		$address_attribute_ids = Kohana::$config->load('common.address_attribute_ids');
		$refs = array();
		$ref = ORM::factory('Reference')

					->where("attribute","IN", $address_attribute_ids)
					->cached(Date::DAY)
					->find_all();
		foreach($ref as $item){
			if (in_array("param_".$item->id, $param_keys))
					$address = $params["param_".$item->id];
		}

		return $address;
	}

	function login()
	{
		$params 	= &$this->params;
		$errors 	= &$this->errors;

		$auth = Auth::instance();


		try {
			$auth->login($params->login, $params->pass, TRUE);
			//echo CI::login($params->login, $params->pass);
		} 
			catch (Exception $e)
		{
			$errors["pass_error"] = $e->getMessage();

		} 
		return $this;
	}

	function check_neccesaries()
	{
		$errors = &$this->errors;
		$category 	= &$this->category;
		$user_id 	= &$this->user_id;

		/*if (!$this->is_edit)
		{
			$count = (int) ORM::factory("Object")
								->where("author","=",$user_id)
								->where("category","=",$category->id)
								->where("is_published","=",1)
								->where("active","=",1)
								->count_all();
			$plan = Plan::check_plan_limit_for_user($user_id, $category->plan_name, $count+1);
			if (!empty($plan)) 
			{
				$errors['plan'] 			= "Вы достигли лимита по количеству объявлений, согласно своего тарифного плана";
				$errors['plan_description'] = Plan::get_plan_error_description($plan->id);
			}
		}*/
		
		
		return $this;
	}

	function parse_object($object, &$params)
	{
		if (! $object->loaded()) return;
		$params->user_text_adv  = $object->user_text;
		$params->title_adv		= $object->title;
		$params->contact  		= $object->contact;

		$params->link_to_company = ($object->author <> $object->author_company_id) ? "on" : "off";

		$location = ORM::factory('Location', $object->location_id);
		if ($location->loaded() && ! $params->address)
			$params->address = $location->address;

		$dl = ORM::factory('Data_List')->where('object', '=', $object->id)->find_all();
		foreach ($dl as $item)
			$params->{"param_".$item->reference} = $item->value;

		$di = ORM::factory('Data_Integer')->where('object', '=', $object->id)->find_all();
		foreach ($di as $item)
			$params->{"param_".$item->reference} = $item->value_min;

		$dn = ORM::factory('Data_Numeric')->where('object', '=', $object->id)->find_all();
		foreach ($dn as $item)
			$params->{"param_".$item->reference} = $item->value_min;

		$dt = ORM::factory('Data_Text')->where('object', '=', $object->id)->find_all();
		foreach ($dt as $item)
			$params->{"param_".$item->reference} = $item->value;

		$oc = ORM::factory('Object_Contact')->where('object_id', '=', $object->id)->find_all();

		foreach ($oc as $item){
			$params->{"contact_".Model_Contact_Type::get_type_name($item->contact_obj->contact_type_id)} = $item->contact_obj->contact;
		}
	}

	function init_instances()
	{
		$params 	= &$this->params;
		$object 	= &$this->object;
		$user 		= &$this->user;
		$user_id 	= &$this->user_id;
		$category 	= &$this->category;
		$city 		= &$this->city;

		$object_id 		= (int) $params->object_id;
		$category_id 	= (int) $params->rubricid;
		$city_id 		= (int) $params->city_id;
		$user 			= Auth::instance()->get_user();

		if ($user)
			$user_id = $user->id;

		if ($object_id > 0)
		{
			$object = ORM::factory('Object', $object_id);
			if ($object->loaded())
			{
				$category_id 	= (int) $object->category;
				$user_id 		= (int) $object->author;
				$city_id 		= (int) $object->city_id;

				if ($params->just_check){
					$this->parse_object($object, $params);
				}

			} else {
				$this->raise_error('object not found');
			}
		}

		if ($city_id > 0)
		{
			$city = ORM::factory('City', $city_id)
								->cached(Date::WEEK, array("region", "add"));
			if ( ! $city->loaded() )
				$this->raise_error('city not finded');
		} 

		if ( $category_id > 0) 
		{ 
			$category = ORM::factory('Category', $category_id)->cached(Date::WEEK, array("category", "add"));
			if ( ! $category->loaded() )
				$this->raise_error('category not finded');
			else 
				$this->category_settings = Kohana::$config->load("category.".$category->id);
		} 
		else 
		{
			//$this->raise_error('undefined category2');	
		}

		if ($user_id > 0)
		{
			$user = ORM::factory('User', $user_id);
			if ( ! $user->loaded() )
				$this->raise_error('user not finded');
		} 

		return $this;
	}

	function init_object_and_mode()
	{
		$params = &$this->params;
		$object = &$this->object;
		$user = &$this->user;
		if ($params->object_id)
		{
			$object = ORM::factory('Object', $params->object_id);
			if ($object->loaded())
			{
				$this->is_edit = TRUE;
				if (!$params->just_check AND !Acl::check_object($object, "object.edit"))
				{
					$this->raise_error('user can\'t edit this ad');
				}
			}
			else
			{
				$this->raise_error('object not found');
			}	
		} else {
			$this->is_edit = FALSE;
			$object = ORM::factory('Object');
		}
		return $this;
	}

	function init_validation_rules()
	{
		$category = &$this->category;
		$validation = &$this->validation;
		$params = &$this->params;
		$category_settings  = new Obj((array) $this->category_settings);

		$validation = Validation::factory((array) $this->params)
			->rule('city_id', 'not_empty', array(':value', "Город"))
			->rule('rubricid', 'not_empty', array(':value', "Раздел"))
			->rule('rubricid', 'not_category_0', array(':value', "Раздел"));

		if ($category)
		{
			$validation->rule('contact', 'not_empty', array(':value', "Контактное лицо"));
		}

		if ($category AND !$category->title_auto_fill AND !$params->itis_massload)
		{
			$validation->rules('title_adv', array(
				array('not_empty', array(':value', "Заголовок")),
				array('min_length', array(':value', 10, "Заголовок")),
			));
		}

		if ($category AND $category->text_required)
		{
			$validation->rules('user_text_adv', array(
				array('not_empty_html', array(':value', "Текст объявления")),
				array('max_length', array(':value', 15000, "Текст объявления")),
			));
		}

		// верифицированы ли контакты
		if ($category AND !$params->just_check)
		{

			$validation->rules('contact_mobile', array(
				array('mobile_verified', array(':value', $params->session_id) )
			));

			$validation->rules('contact_phone', array(
				array('phone_verified', array(':value', $params->session_id) )
			));

			$validation->rules('contact_email', array(
				array('email_verified', array(':value', $params->session_id) )
			));

		} 
		elseif ($category AND $category_settings->one_mobile_phone AND !$params->itis_massload  AND !$params->just_check)
		{
			$validation->rules('contact_mobile', array(
				array('mobile_verified', array(':value', $params->session_id) )
			));
		}

		return $this;
	}

	function init_additional()
	{
		$category = &$this->category;
		$validation = &$this->validation;
		$params = &$this->params;
		$user = &$this->user;

		if ($user AND $category AND $settings = Kohana::$config->load("category.".$category->id.".additional_fields.".$user->org_type))
		{

			$titles =  Kohana::$config->load("dictionaries.additional_fields.".$user->org_type);
			foreach ($settings as $setting) {
				$validation->rules($setting, array(
						array('not_empty', array(':value', $titles[$setting]))
					)
				);
			}
		}

		if ($category AND $saveas = Kohana::$config->load("category.".$category->id.".additional_saveas"))
		{
			if (!$saveas)
				$saveas = array();

			foreach ($saveas as $field => $_saveas) {

					$param = $saveas[$field][0];
					$value = trim($params->{$field});
					if ($value)
						$params->{$param} = $value;
					else
						$params->{$param} = $saveas[$field][1];
			}
		}
		return $this;
	}

	function init_contacts()
	{
		$contacts = &$this->contacts;
		$category = &$this->category;

		$params = $this->params;
		foreach (array("mobile","phone","email") as $type_name) {
			$type_id = Model_Contact_Type::get_type_id($type_name);
			$value = $params->{"contact_".$type_name};
			if (!$value) continue;
			$contact = ORM::factory('Contact')->by_value($value)->find();
			
			$contacts[] = array(
				'contact_obj' 	=> $contact,
				'value' 		=> $value,
				'type_id' 	=> $type_id
			);

		}

		//append additional contacts
		$this->init_additional_contacts();

		return $this;
	}

	function init_additional_contacts() {
		if (!is_array($this->params->additional_contacts)) {
			return $this;
		}
		
		foreach($this->params->additional_contacts as $contact) {
			if (empty($contact['value'])) {
				continue;
			}
			$this->contacts []= array(
					'value' => $contact['value'],
					'type_id' => $contact['type'],
					'contact_obj' => ORM::factory('Contact')->by_value($contact['value'])->find()
				);
		}

		return $this;
	}

	function check_signature()
	{
		$params = &$this->params;
		$errors = &$this->errors;
		$object = &$this->object;
		$category = &$this->category;
		$user     = &$this->user;
		$contacts = &$this->contacts;

		if (!$category OR !$user) return $this;

		if ($this->is_just_triggers($params))
			@list($values, $list_ids) = (array) Object_Utils::get_parsed_parameters(NULL, $object->id, TRUE);
		else 
			@list($values, $list_ids) = (array) Object_Utils::get_parsed_parameters($params, NULL, TRUE);

		$this->signature 				= ($this->is_union_enabled()) ?
													$this->generate_signature("", "",$values) : 
														$this->generate_signature($params->title_adv, $params->user_text_adv, $values);

		$this->signature_full = $this->generate_signature($params->title_adv, $params->user_text_adv, $values);														

		$this->options_exlusive_union 	= $this->get_options_exlusive_union($params->address, $params->city_id, $category->id, $list_ids);

		$this->list_ids = $list_ids;

		if ( $this->is_edit AND $params->itis_massload ){

			$sign_existed = ORM::factory('Object_Signature')->where('object_id','=',$object->id)->find();
			if ($sign_existed->loaded())
			{
				// $attachments_count = ORM::factory('Object_Attachment')->where("object_id","=",$object->id)->count_all();
				// $input_attachament_count = count($params->userfile);

				// $isChanged = ORM::factory('Object_Contact')
				// 				->where("object_id","=",$object->id)
				// 				->compare($contacts);


				$signature_full = '{'.join(',', $this->signature_full).'}';
				$sign_existed = str_replace('"','',$sign_existed->signature_full);

				if ($signature_full == $sign_existed)
						//AND $attachments_count == $input_attachament_count)
				{
					$errors['nochange'] = "Объявление не требует обновления.";	
					$this->union_cancel = TRUE;
				}
			}
		}

		if ($this->is_similarity_enabled())
		{
			$max_similarity = Kohana::$config->load('common.max_object_similarity');
			$similarity 	= ORM::factory('Object_Signature')->get_similarity($max_similarity,$this->signature_full, NULL, $params->object_id, $user->id, "_full", $params->itis_massload);
			
			if ($similarity["sm"] > $max_similarity){
				
				if ( $this->is_edit ){
					if ($params->just_check){
						$errors['signature'] = "Такое объявление у вас уже есть, дубли запрещены правилами сайта.";	
						$object->is_published = 0;	
					}					
					//$object->is_bad = 2;	
					//$object->date_updated = date('Y-m-d H:i:s');
					$this->union_cancel = TRUE;
				} else {
					$errors['signature'] = "Такое объявление у вас уже есть, дубли запрещены правилами сайта.";	
				}
			}
		}

		return $this;
	}

	// function check_signature_for_union()
	// {
	// 	$params = &$this->params;
	// 	$object = &$this->object;
	// 	$errors = &$this->errors;
	// 	$category = &$this->category;
	// 	$user     = &$this->user;

	// 	if (!$category OR !$user) return $this;

	// 	if ($this->is_union_enabled() AND $this->is_union_enabled_by_category($category->id) AND !$this->union_cancel)
	// 	{
	// 		$max_similarity = Kohana::$config->load('common.max_object_similarity');
	// 		$similarity 	= ORM::factory('Object_Signature')->get_similarity($max_similarity, $this->signature, $this->options_exlusive_union, $params->object_id);

	// 		if ($similarity["sm"] >= $max_similarity){

	// 			$valid = $this->validate_between_parameters($category->id, $this->list_ids, $similarity["object_id"]);
	// 			if (!$valid)
	// 			{
	// 				$this->destroy_union = TRUE;
	// 				return $this;
	// 			}

	// 			$parent_id = (int) ORM::factory('Object', $similarity["object_id"])->parent_id;
	// 			if ($parent_id == 0)
	// 			{
	// 				$this->create_union = TRUE;
	// 				$this->object_without_parent_id = $similarity["object_id"];
	// 			} else {
	// 				$this->edit_union = TRUE;
	// 				$this->parent_id = $parent_id;
	// 			}				
	// 		} 
	// 			else if ($this->is_edit AND $object->parent_id)
	// 		{
	// 			$this->destroy_union = TRUE;
	// 		}
	// 	}

	// 	return $this;
	// }

	// function save_union()
	// {
	// 	$params = &$this->params;
	// 	$object = &$this->object;
	// 	$errors = &$this->errors;
	// 	$category = &$this->category;
	// 	$user     = &$this->user;

	// 	if (!$category OR !$user) return $this;

	// 	if ($this->is_union_enabled() AND $this->is_union_enabled_by_category($category->id))
	// 	{ 
	// 		$parent_id = 0;

	// 		if ($this->destroy_union)
	// 		{
	// 			$union_object = ORM::factory('Object', $object->parent_id);
	// 			if ($union_object->loaded())
	// 			{
	// 				$this->parent_id = $union_object->id;
	// 				if ($union_object->is_union <= 2)
	// 				{
	// 					ORM::factory('Object')
	// 						->where('parent_id','=', $union_object->id)
	// 						->set('parent_id',  DB::expr('NULL'))
	// 						->update_all();
	// 					DB::delete('object')->where("id","=",$union_object->id)->execute();						
	// 				}
	// 				else if ($union_object->is_union > 2)
	// 				{
	// 					ORM::factory('Object')
	// 						->where('id','=', $object->id)
	// 						->set('parent_id',  DB::expr('NULL'))
	// 						->update_all();
	// 					$this->edit_union = TRUE;		
	// 				}
	// 			}
	// 		}

	// 		if ($this->create_union OR $this->edit_union)
	// 		{
	// 			//ry {
	// 				$this->original_params["object_id"] = $this->parent_id;
	// 				$this->original_params["rubricid"]  = $category->id;
	// 				$this->original_params["city_id"]   = $object->city_id;

	// 				$objects_for_union = Array(
	// 						"initial_object" 		 => $this->object_without_parent_id,
	// 						"current_object_source"  => $object->id
	// 					);

	// 				$parent_id = (int) Object::PlacementAds_Union($this->original_params, $objects_for_union, $this->edit_union, $this->destroy_union);
	// 			//}
	// 			//catch (Exception $e) {
	// 			// 	$errors['union_error'] = $e->getMessage();
	// 			//}
					
	// 		}
			
	// 		if ($parent_id >0 )	
	// 			$this->parent_id = $parent_id;

	// 		if ($this->destroy_union)	
	// 			$this->parent_id = NULL;
			

	// 	}		

	// 	return $this;

	// }
	/**
	 * [normalize_attributes Приведение пользовательских данных в порядок, trim, replace и прочее]
	 * @return [this]
	 */
	function normalize_attributes()
	{
		$category 		 = &$this->category;
		$postparams 	 = &$this->params;

		if (!$category) return $this;

		try {
			$_params = preg_grep("/^param_/", array_keys((array) $postparams));
			

			foreach ($_params as $_param) {
				@list($_t, $reference_id) =  explode("_", $_param);

				$reference = ORM::factory('Reference')
								->with_attribute_by_id($reference_id)
								->cached(Date::WEEK)
								->find();
				if (!$reference->loaded())
					continue;

				if (is_array($postparams->{"param_".$reference_id}))
					continue;
				
				$postparams->{"param_".$reference_id} = trim($postparams->{"param_".$reference_id});

				switch ($reference->type)
				{
					case 'integer':
						$postparams->{"param_".$reference_id} = trim($postparams->{"param_".$reference_id});
						$postparams->{"param_".$reference_id} = preg_replace('/[^0-9]/', '', $postparams->{"param_".$reference_id});
					break;
					case 'numeric':
						$postparams->{"param_".$reference_id} = trim(str_replace(",", ".", $postparams->{"param_".$reference_id}));
					break;
				}
			}
		} catch (Exception $e)
		{
			Kohana::$log->add(Log::NOTICE, $e->getMessage());
		}

		return $this;
	}

	function init_validation_rules_for_attributes()
	{

		$category 		 = &$this->category;
		$postparams 	 = &$this->params;
		$user     		 = &$this->user;

		if (!$category) return $this;

		$form_references = Forms::get_by_category($category->id, $postparams);

		foreach ($form_references as $reference)
		{
			$reference_id = $reference->reference_id;
			$rules = array();

			if ($reference->is_range)
			{
				$params = array(
					'param_'.$reference_id.'_min',
					'param_'.$reference_id.'_max',
				);
			}
			else
			{
				$params = array('param_'.$reference_id);
			}

			if ($reference->is_required)
			{

				$rules[] = array('not_empty', array(':value', $reference->attribute_title));
			}

			if (!$reference->is_ilist)
				switch ($reference->attribute_type)
				{
					case 'integer':
						$rules[] = array('digit', array(':value', $reference->attribute_title));
						$rules[] = array('min_value', array(':value', $reference->attribute_title, 0));
						$rules[] = array('max_value', array(':value', $reference->attribute_title, 999999999));
					break;

					case 'numeric':
						$rules[] = array('numeric', array(':value', $reference->attribute_title));
						$rules[] = array('min_value', array(':value', $reference->attribute_title, 0));
						$rules[] = array('max_value', array(':value', $reference->attribute_title, 999999999));
					break;

					case 'text':
						$rules[] = array('max_length', array(':value', $reference->attribute_max_text_length, $reference->attribute_title));
					break;
				}
			// @todo check xss validation

			foreach ($params as $param)
			{
				$this->validation->rules($param, $rules);
			}
		}
		return $this;		
	}

	function exec_validation()
	{
		$errors 		 	= &$this->errors;
		$user 			 	= &$this->user;
		$category 		 	= &$this->category;
		$params 		 	= &$this->params;
		$category_settings  = new Obj((array) $this->category_settings);

		//заполнены ли обязательные параметры
		if ( !$this->validation->check())
		{
			if (!$errors)
				$errors = array();

			$errors = array_merge($errors, $this->validation->errors('validation/object_form'));
		}

		if ($params->video)
		{
			$youtube = '@youtu(?:(?:\.be/([_\-A-Za-z0-9]+))|(?:be.com/(?:(?:watch\?v=)|(?:embed/))([\-A-Za-z0-9]+)))@i';
			

			if ( !preg_match($youtube, $params->video, $matches) ) {
				$errors['video'] = 'Неподдерживаемый видеохостинг. Или неправильная ссылка на видео';
			}
		}

		if (!$user OR ($user AND !$user->loaded()))
		{
			$errors['not_autorized'] =  Kohana::message('validation/object_form', 'not_autorized');
		}

		if (count($this->contacts) == 0)
		{
			$errors['contact_mobile'] = "Необходимо добавить хотя бы один верифицированный контакт для связи";
		} else {
			if (!$params->just_check) {
				foreach ($this->contacts as $contact_item) {
					$contact = $contact_item["contact_obj"];
					$contact_validation = $contact->check_contact($params->session_id, $contact_item["value"], $contact_item["type_id"], FALSE, TRUE);
					if (!$contact_validation->check())
					{	
						$errors = array_merge($errors, $contact_validation->errors('validation/object_form'));
					}
				}
			}
		}
		
		if ($category AND !$category_settings->phone_or_mobile_notrequired AND !$params->itis_massload AND !$params->contact_mobile AND !$params->contact_phone)
		{
			$errors['contact_mobile'] = "Для этой рубрики, необходимо обязательно указать телефон";
			$errors['contact_phone'] = "Для этой рубрики, необходимо обязательно указать телефон";
		}
		elseif ($category AND $category_settings->one_mobile_phone AND !$params->itis_massload AND !$params->contact_mobile)
		{
			$errors['contact_mobile'] = "Для этой рубрики, необходимо обязательно указать мобильный телефон";
		}

		//если пользователь привязан к компании и подает объявления как от компании то не проверяем количество поданных
		if ($user AND $category AND (!$user->linked_to_user OR !isset($params->link_to_company))) 
		{
			if ($user->org_type == 1)
			{
				$is_excess = $user->is_excess_max_count_objects_in_category($category, $this->params->object_id);
				// проверяем количество уже поданных пользователем объявлений
				if ($is_excess)
				{
					$errors['max_objects_for_user'] = Kohana::message('validation/object_form', 'max_objects');
				}
			} else {
				$limit = ORM::factory('Category')->get_individual_limited($user->id, $category->id);
				if (count($limit)>0){
					$limit = $limit[0]["individual_limit"];
					$errors['max_objects_for_company'] = Kohana::message('validation/object_form', 'max_objects_company');
				}
			}
		}

		if ( $category AND $category_settings->max_count AND
					$category_settings->max_count <=
						$this->category->get_count_active_object_in_category($user, $this->params->object_id))
		{
			$errors['max_objects_for_user'] = "В эту рубрику можно разместить только одно объявление.";
			if ($this->is_edit)
				$errors['max_objects_for_user'] .= " Снимите другие объявления в этой рубрике, для того чтобы его можно было отредактировать/поднять/продлить";
		}

		return $this;
	}

	function save_address()
	{
		$params = &$this->params;
		$city = &$this->city;
		$location = &$this->location;
		$object = &$this->object;
		

		if (!$city->loaded()) {
			$this->raise_error('При сохранении, не указан город');
		}

		$location = ORM::factory('Location');
		$region_title = $city->region->title;
		$city_title = $city->title;
		$address = trim($params->address);

		if ($params->real_city_exists) {
			$city_title = $params->real_city;
		}

		@list($lat, $lon) = explode(',', $params->object_coordinates);
		if ( ! $lat OR ! $lon OR $params->real_city_exists)
		{
			// если координаты не пришли, запрашиваем координаты по адресу
			@list($coords, $region_title, $address) = Ymaps::instance()->get_coord_by_name($city_title.', '.$address);
			$city_title = $region_title;
			@list($lon, $lat) = $coords;
		}

		if ($address)
		{
			$loc_count = 0;
			if ($object->location_id)
				$loc_count = ORM::factory('Object')->where("location_id","=", $object->location_id)->count_all();

			if ($loc_count == 1 AND $object->location_id <> $city->location_id)
				$location = $location->where("id","=",$object->location_id)->find();

			$location->region 	= $region_title;
			$location->city 	= $city_title;
			$location->address 	= $address;
			$location->lat 		= $lat;
			$location->lon 		= $lon;
			$location->save();
		}

		// если не нашли адрес, то берем location города
		if ( ! $location->loaded() )
			$location = $city->location;

		return $this;
	}

	function save_many_cities()
	{
		$city = &$this->city;
		$object = &$this->object;

		if ($object->loaded())
		{
			// если изменился основной город, то меняем его и в списке доп городов
			if ($city->id != $object->city_id)
			{
				if ( ! $cities = $object->get_cities())
				{
					$object->cities = array($city->id);
				}
				else
				{
					// меняем только основной город в списке
					foreach ($cities as $key => $city_id)
					{
						if ($city_id == $object->city_id)
						{
							if ( ! in_array($city->id, $cities))
							{
								$cities[$key] = $city->id;
							}
						}
					}

					$object->cities = $cities;
				}
			}
		}
		return $this;
	}

	function prepare_object()
	{
		$object = 	&$this->object;
		$params = 	&$this->params;
		$city = 	&$this->city;
		$location = &$this->location;
		$category = &$this->category;
		$user = 	&$this->user;

		if ($params->default_action)
		{
			$object->action 		= $params->default_action;
		}
		$object->category 			= $category->id;
		$object->contact 			= $params->contact;
		$object->city_id			= $city->id;
		$object->ip_addr 			= Request::$client_ip;
		
		if ( ! $this->is_edit) // если это редактирвоание, то is_published не трогаем
		{
			$object->is_published 	= $user->loaded() ? 1 : 0;
		}

		if ( ! $category->title_auto_fill)
		{
			$object->title 			= $params->title_adv;
		}

		if ($params->from_company AND $user->linked_to->loaded())
		{
			$object->author_company_id = $user->linked_to->id;
		}
		elseif ( ! $this->is_edit)
		{
			$object->author_company_id = $user->id; //DB::expr('NULL');
		}

		if ( ! $this->is_edit)
		{
			// при редактировании автора не меняем
			$object->author 			= $user->id;
		}
		//filter text only if user is not admin
		if (!\Yarmarka\Models\User::current()->isAdminOrModerator()) {
			$object->user_text 			= Text::clear_usertext_tags($params->user_text_adv);
		} else {
			$object->user_text = $params->user_text_adv;
		}
		if ( ! $this->is_edit)
		{
			$object->date_expiration	= $this->lifetime_to_date($params->lifetime);
		}
		$object->geo_loc 			= $location->get_lat_lon_str();
		$object->location_id		= $location->id;

		return $this;
	}

	function save_parentid_object()
	{
		$params 	= &$this->params;
		$object 	= &$this->object;
		$category 	= &$this->category;

		if ($this->is_union_enabled() AND $this->is_union_enabled_by_category($category->id))
		{
			//if (!empty($this->parent_id))
			//{
				$object->parent_id = ($this->parent_id == 0) ? NULL : $this->parent_id;
			//}
		}
	}

	function save_object()
	{
		$object = &$this->object;
		// сохраняем объявление
		$object->save();
		//при сохранении срабатывает триггер в самой модели
		return $this;		
	}

	function save_photo()
	{
		$params = &$this->params;
		$object = &$this->object;

		// удаляем старые аттачи
		// @todo по сути не надо заного прикреплять те же фотки при редактировании объявления
		ORM::factory('Object_Attachment')->where('object_id', '=', $object->id)->delete_all();

		// собираем аттачи
		if ($userphotos = $params->userfile AND is_array($userphotos))
		{
			$max_count_photo = Kohana::$config->load('common.max_count_photo');
			$userphotos = array_slice($userphotos, 0, $max_count_photo);
			$main_photo = $params->active_userfile;
			if ( ! $main_photo AND isset($userphotos[0]))
			{
				$main_photo = $userphotos[0];
			}
			$userphotos = array_reverse($userphotos);
			foreach ($userphotos as $file)
			{
				$attachment = ORM::factory('Object_Attachment');
				$attachment->filename 	= $file;
				$attachment->object_id 	= $object->id;
				$attachment->signature 	= TRUE;
				$attachment->save();

				if ($file == $main_photo)
				{
					$object->main_image_id = $attachment->id;
				}
			}

			// удаляем аттачи из временой таблицы
			foreach ($userphotos as $file) 
			{
				ORM::factory('Tmp_Img')->delete_by_name($file);
			}
		}
		return $this;
	}

	function save_video()
	{
		$params = &$this->params;
		$object = &$this->object;

		if ($params->video AND $params->video_type)
		{
			$video = $params->video;

			$youtube = '@youtu(?:(?:\.be/([_\-A-Za-z0-9]+))|(?:be.com/(?:(?:watch\?v=)|(?:embed/))([\-A-Za-z0-9]+)))@i';
			$filename = '';
			$error = NULL;

			if ( preg_match($youtube, $video, $matches) ) {//youtube
					if ( !empty($matches[1]) ) {
						$filename = $matches[1];
					} else {
						$filename = $matches[2];
					}
				
				$attachment = ORM::factory('Object_Attachment');
				$attachment->filename 	= $filename;
				$attachment->type 		= $params->video_type;
				$attachment->object_id 	= $object->id;
				$attachment->save();
			}
		}
		return $this;
	}

	function save_price()
	{
		$params = &$this->params;
		$object = &$this->object;
		$category = &$this->category;

		if (!$category) return $this;

		if (in_array("pricelist", array_keys((array) $params)))
		{
			$params->pricelist = (int) $params->pricelist;

			$price_enabled = FALSE;
			$settings = Kohana::$config->load("category.".$category->id);
			if ($settings)
			{
				$settings = new Obj($settings);
				if ($settings->price_enabled)
					$price_enabled = TRUE;
			}

			if (!$price_enabled AND !$params->pricelist)
				return $this;

			$op = ORM::factory('Object_Priceload');
			$op->where("object_id","=",$object->id)->delete_all();

			if ($params->pricelist)
			{
				$price = ORM::factory('Priceload', $params->pricelist);
				if (!$price->loaded())
					return;

				$op->object_id = $object->id;
				$op->priceload_id = $params->pricelist;
				$op->save();
			}
			
		}
		return $this;
	}

	function save_signature()
	{
		$object = &$this->object;

		if ($this->signature)
		{
			$object_signature = ORM::factory('Object_Signature')
						->where('object_id', '=', $object->id)
						->find();
			$object_signature->object_id  				= $object->id;
			$object_signature->signature  				= $this->signature;
			$object_signature->signature_full  			= $this->signature_full;
			$object_signature->options_exlusive_union   = $this->options_exlusive_union;
			$object_signature->save();
		}
		return $this;
	}

	function save_other_options()
	{
		$params = &$this->params;
		$object = &$this->object;
		$user   = &$this->user;

		

		// отключаем комментарии к объявлению
		if ($params->block_comments)
		{
			$object->disable_comments();
		}

		if ($user AND $user->linked_to_user AND isset($this->params->link_to_company))
		{
			$object->author_company_id = $user->linked_to_user;
		} elseif ($user AND $user->linked_to_user AND !isset($this->params->link_to_company))
		{
			$object->author_company_id = $user->id;
		}


		if ($this->is_edit AND $params->publish_and_prolonge)
		{
			if ($object->is_bad <> 2)
			{
				$object->is_published = 1;
				if ($object->in_archive)
				{
					$object->prolong($this->lifetime_to_date("3m"));
				} else 
				{
					$object->date_expiration = $this->lifetime_to_date("3m");
				}
			}
		}
		return $this;
	}

	function save_additional()
	{
		$params = &$this->params;
		$object = &$this->object;
		$user = &$this->user;

		if ($user AND $object->category AND $settings = Kohana::$config->load("category.".$object->category.".additional_fields.".$user->org_type))
		{
			foreach ($settings as $setting) {
				$name = str_replace("additional_", "", $setting);
				$value = $params->{$setting};

				if (!$value)
					ORM::factory('User_Settings')
						->_delete($user, "orginfo", $name);
				
				ORM::factory('User_Settings')
						->update_or_save($user, "orginfo", $name, $value);
			}
		}

		return $this;
	}

	function save_attributes()
	{
		$params = &$this->params;
		$object = &$this->object;

		$attributes = Object_Utils::prepare_form_elements((array) $params);

		$boolean_deleted = FALSE; // если меняются булевые параметры, то удаляем все что есть в базе
		foreach ($attributes as $reference_id => $value)
		{	//В случае нескольких значений(is_multiple)
			$value_detail = (is_array($value) and isset($value[0])) ? $value[0] : $value;

			if ((!is_array($value_detail)) AND ($value_detail>0))
			{
				$action = ORM::factory('Attribute_Action')
						->where('value_id','=',intval($value_detail))
						->cached(Date::WEEK, array("add","category"))
						->find();
				if ( $action->loaded() )
				{
					$object->action = $action->action_id;
				}
			}

			$reference = ORM::factory('Reference')
				->where("id","=",$reference_id)
				->set_time_link_cache(15)
				->cached(Date::WEEK, array("add","relation"))
				->find();

			if ( ! $reference->loaded())
			{
				// неизвестный элемент формы
				continue;
			}

			if ($reference->attribute_obj->type == 'boolean' AND ! $boolean_deleted)
			{
				ORM::factory('Data_Boolean')->where('object', '=', $object->id)->delete_all();
				$boolean_deleted = TRUE;
			}

			// удаляем старые значения
			ORM::factory('Data_'.Text::ucfirst($reference->attribute_obj->type))
				->where('object', '=', $object->id)
				->where('reference', '=', $reference->id)
				->delete_all();

			// проверяем есть ли значение
			if (is_array($value)) 
			{					
				//Условие №1 игнорирования дальнейшей обработки значения
				$fail_cond1 = (empty($value['min']) AND empty($value['max']));					
				//Условие №2 игнорирования дальнейшей обработки значения
				$fail_cond2 = !isset($value[0]);
				if (!$fail_cond2) $fail_cond2 = empty($value[0]);
									
				if ($fail_cond1 AND $fail_cond2) 
					continue;					
			}
			elseif (empty($value))
			{
				//Для цены допускаем ноль
				if (!$reference->attribute_obj->is_price)
					continue;
			}

			// сохраняем цену для объявления
			if ($reference->attribute_obj->is_price)
			{
				if (is_array($value) and isset($value['min']))
				{
					$object->price = $value['min'];
				}
				else
				{
					$object->price = $value;
				}
				$object->price_unit = $reference->attribute_obj->unit;
			}


			// сохраняем дата атрибут
			$data = ORM::factory('Data_'.Text::ucfirst($reference->attribute_obj->type));
			$data->attribute 	= $reference->attribute;
			$data->object 		= $object->id;
			$data->reference 	= $reference->id;
			if ($data->is_range_value())
			{
				if (is_array($value))
				{
					$data->value_min = $value['min'];
					$data->value_max = $value['max'];
				}
				else
				{
					$data->value_min = $value;
				}
			}
			else
			{
				$data->value = $value;
			}
			//Значения для множественных атрибутов(с учетом того, что is_multiple могут быть только list)
			if (is_array($value) and isset($value[0]))
			{
				foreach ($value as $value_detail) 
				{
					$data2 = clone $data;
					$data2->value = (int)$value_detail;
					$data2->save();
				}
			}
			else
			{
				$data->save();
			}
		}
		return $this;
	}

	function save_generated()
	{
		$object = &$this->object;
		$params = &$this->params;

		if ($object->category_obj->title_auto_fill)
		{
			$object->title = $object->generate_title();
		} else {
			$object->title = strip_tags($object->title);
		}

		$object->full_text = $object->generate_full_text();
		$object->save();
		return $this;
	}

	function save_service_fields()
	{
		$params = &$this->params;
		$object = &$this->object;
		


		return $this;
	}

	function save_compile_object()
	{
		$object = &$this->object;
		$params = &$this->params;

		$compiled = Object_Compile::saveObjectCompiled($this->object, $params);

		$_object = ORM::factory('Object', $object->id)->get_row_as_obj();
		$_object->compiled = $compiled;

		Cache::instance('object')->delete($_object->id);
		Cache::instance('object')->set($_object->id, (array) $_object, 3600);
		
		return $this;
	}

	function send_external_integrations()
	{
		$object = &$this->object;
		$object = ORM::factory('Object', $object->id);
		// сохраняем запись для короткого урла *.ya24.biz
		Model_Object::send_to_db_dns($object->id);

		if ( ! $this->is_edit && !$object->type_tr) 
		{
			//пишем id объявления во временную таблицу для последующего обмена с terrasoft
			$object->send_to_terrasoft();
		}

		return $this;
	}

	function send_message()
	{
		$object = &$this->object;
		$params = &$this->params;
		$city = &$this->city;
		$category = &$this->category;
		$user = &$this->user;
		$contacts = &$this->contacts;
		if ($user->email)
		{
			// отправляем уведомление о успешном редактировании/публикации
			$is_edit = $this->is_edit;
			$subj = $this->is_edit 
				? 'Вы успешно изменили Ваше объявление.' 
				: 'Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!';			

			$msg = View::factory('emails/add_notice',
					array('is_edit' => $is_edit,'object' => $object, 'name' => $user->get_user_name(), 
						'obj' => $object, 'city' => $city, 'category' => $category, 'subdomain' => Region::get_domain_by_city($city->id), 
						'contacts' => $contacts, 'address' => $params->address_str));

			Email::send($user->email, Kohana::$config->load('email.default_from'), $subj, $msg);
		}	

		return $this;	
	}

	function send_to_forced_moderation()
	{
		$object = &$this->object;
		if ($object->is_bad === 1)
		{
			// если объявление было на исправлении, то отправляем на модерацию
			$object->to_forced_moderation();
		}	

		return $this;
	}

	function save_contacts()
	{
		$object = &$this->object;
		$user = &$this->user;
		$contacts = &$this->contacts;

		if ($this->is_edit)
		{	
			// удаляем связи на старые контакты
			$object->delete_contacts();
		}

		foreach ($contacts as $contact)
		{
			// сохраняем контакты для пользователя
			$user->add_verified_contact($contact['type_id'], $contact['value']);

			// сохраянем новые контакты для объявления
			$object->add_contact($contact['type_id'], $contact['value']);
		}

		return $this;
	}

	function init_defaults()
	{
		$this->contacts = array();
	}

	private static function is_nessesary_to_check($category_id, $reference_id, $postparams)
	{
		$return = TRUE;

		$ar = ORM::factory('Attribute_Relation')
					->where("attribute_relation.category_id","=", $category_id)
					->where("attribute_relation.reference_id","=", $reference_id)
					//->where("attribute_relation.parent_id","IS NOT", NULL)
					->where("attribute_relation.is_required","=", 1)
					->cached(Date::DAY)
					->find();

		if (!array_key_exists("param_".$ar->reference_id, (array)$postparams) )
			$return = FALSE;		

		return $return;
	}

	private static function raise_error($text){
		throw new HTTP_Exception_404($text);		
	}

	private static function lifetime_to_date($lifetime)
	{
		switch ($lifetime) 
			{
				case "1m":
					$date_expiration = date('Y-m-d H:i:s', strtotime('+1 month'));
				break;
				case "2m":
					$date_expiration = date('Y-m-d H:i:s', strtotime('+2 month'));
				break;
				case "3m":
					$date_expiration = date('Y-m-d H:i:s', strtotime('+3 month'));
				break;
				default:
					$date_expiration = date('Y-m-d H:i:s', strtotime('+14 days'));
				break;
			}
		return $date_expiration;
	}

	private static function get_union_config($category = 0)
	{
		$config = NULL;
		if ($category >0)
		{
			try {
				$config = Kohana::$config->load('union.'.$category);
			} catch (Exception $e)
			{
				$config = NULL;
			}
		}
		return $config;
	}

	public static function validate_between_parameters($category_id, $list_ids = Array(), $similar_object_id)
	{
		$result = TRUE;
		$config = self::get_union_config($category_id);
		if ($config)
		{
			foreach ($config["between_params"] as $attribute_id=>$procent)
			{
				$attribute = ORM::factory('Attribute')
								->where("id","=",$attribute_id)
								->cached(Date::DAY)
								->find();
				if ($attribute->loaded())
				{
					if ($attribute->type == "integer")
					{
								$di = ORM::factory('Data_Integer')
									->where("object","=",$similar_object_id)
									->where("attribute","=",$attribute->id)
									->find();

					} else
					if ($attribute->type == "numeric")
					{
							$di = ORM::factory('Data_Numeric')
								->where("object","=",$similar_object_id)
								->where("attribute","=",$attribute->id)
								->find();
					}

					$similar_value = $di->value_min;
					$odds = ($similar_value/100)*$procent;
					$input_value   = $list_ids[$attribute_id];
					//throw new Exception("val ".$similar_value." odds ".$odds." inp ".$input_value, 1);
					
					if ($input_value <= $similar_value-$odds
						OR $input_value >= $similar_value+$odds)
					{
						$result = FALSE;
						break;
					}
					
				}

			}

		}
		return $result;
	}

	private static function get_options_exlusive_union($address, $city_id, $category_id, $list_ids = Array())
	{
		$return = Array();
		if ($address){
			$address_signature = Object_Utils::generate_signature( $address );
			$return = array_merge($return, $address_signature);
		}
		if ($city_id)
			$return[] = $city_id;
		if ($category_id)
			$return[] = $category_id;

		$config = self::get_union_config($category_id);

		if ( is_array($config) ) 
		{			
			foreach ($list_ids as $key=>$item)
			{
				if (in_array($key, $config['options_exlusive_union']))
				{
					if ((int) $item > 0) {
						$return[] = (int) $item;
					}
				}
			}
		}

		asort($return);

		return "{".join(',', $return)."}";
	}

	private static function generate_signature($title = "", $text = "", $values = Array())
	{
		$values[] = strip_tags($title);
		$values[] = strip_tags($text);

		return Object_Utils::generate_signature( join(", ", $values) );
	}

	private static function is_union_enabled()
	{
		return Kohana::$config->load('common.union_objects_by_similarity');
	}

	private static function is_union_enabled_by_category($category_id)
	{
		return (in_array($category_id, Kohana::$config->load('common.union_objects_by_similarity_by_cat')));
	}

	private static function is_similarity_enabled()
	{
		return Kohana::$config->load('common.check_object_similarity');
	}

	private static function is_just_triggers($params)
	{
		return (property_exists($params, 'only_run_triggers') AND $params->only_run_triggers == 1);
	}
}
