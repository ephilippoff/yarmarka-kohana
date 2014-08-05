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
	public $location;
	public $signature = NULL;
	public $original_params;
	public $is_edit = NULL;

	public $parent_id;
	public $create_union = FALSE;
	public $edit_union = FALSE;
	public $object_without_parent_id = NULL;

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
			//foreach($params as $key=>$value)
			//{
				$this->params = new Obj($params);//->{$key} = $value;	
			//}
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
		$i = 0;
		foreach ($oc as $item){
			$params->{"contact_".$i."_value"} = $item->contact_obj->contact;
			$params->{"contact_".$i."_type"} = $item->contact_obj->contact_type_id;
			$i++;
		}
	}

	function init_instances()
	{
		$params 	= &$this->params;
		$object 	= &$this->object;
		$user 		= &$this->user;
		$user_id 	= &$this->user_id;
		$category 	= &$this->category;

		$object_id 		= (int) $params->object_id;
		$category_id 	= (int) $params->rubricid;
		$user 			= Auth::instance()->get_user();
		$user_id 	    = $user->id;

		if ($object_id > 0)
		{
			$object = ORM::factory('Object', $object_id);
			if ($object->loaded())
			{
				$category_id 	= (int) $object->category;
				$user_id 		= (int) $object->author;
				$params->city_id = $object->city_id;

				if ($params->just_check){
					$this->parse_object($object, $params);
				}

			} else {
				$this->raise_error('object not found');
			}
		}

		//затычка на основной форме подачи, пока город берется из кладра
		if (!$params->city_id AND $params->city_kladr_id)
			$params->city_id = ORM::factory('City')->where("kladr_id","=",$params->city_kladr_id)->find()->id;
		if ($params->city_id AND !$params->city_kladr_id)
			$params->city_kladr_id = ORM::factory('City',$params->city_id)->kladr_id;

		if ( $category_id > 0) 
		{ 
			$category = ORM::factory('Category', $category_id);
			if ( ! $category->loaded() )
				$this->raise_error('category not finded');
		} 
		else 
		{
			$this->raise_error('undefined category2');	
		}

		if ($user_id > 0)
		{
			$user = ORM::factory('User', $user_id);
			if ( ! $user->loaded() )
				$this->raise_error('user not finded');
		} 
		elseif ($user->role == 1 OR $user->role == 3)
		{

		} /*else 
		{
			$this->raise_error('user dont have permissions to edit this object');
		}*/

		$this->form_references = Forms::get_by_category_and_type($this->category->id, 'add');

		$this->conditions = Forms::get_category_conditions($this->category->id);

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
				if ( ! $user->can_edit_object($object->id) AND !$object->is_union)
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

		$validation = Validation::factory((array) $this->params)
			->rule('city_kladr_id', 'not_empty', array(':value', "Город"))
			->rule('contact', 'not_empty', array(':value', "Контактное лицо"))
			->rule('email', 'email');	

		if ( ! $category->title_auto_fill)
		{
			$validation->rules('title_adv', array(
				array('not_empty', array(':value', "Заголовок")),
				array('min_length', array(':value', 15)),
			));
		}

		if ($category->address_required)
		{
			$validation->rule('address', 'not_empty', array(':value', "Адрес"));
		}

		if ($category->text_required)
		{
			$validation->rules('user_text_adv', array(
				array('not_empty', array(':value', "Текст объявления")),
				array('max_length', array(':value', 15000)),
			));
		}
		return $this;
	}

	function init_contacts()
	{
		$contacts = &$this->contacts;
		foreach((array) $this->params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$value = trim($this->params->{'contact_'.$matches[1].'_value'});
				$type = $this->params->{'contact_'.$matches[1].'_type'};
				if ($value)
				{
					$contact_type 	= ORM::factory('Contact_Type', $type );
					$contact 		= ORM::factory('Contact')->by_contact_and_type($value, $contact_type->id)
						->find();
					if ($contact_type->loaded() AND $contact->is_verified($this->params->session_id))
					{
						$contacts[$contact->id] = array(
							'contact_obj' 	=> $contact,
							'value' 		=> $value,
							'type' 			=> $contact_type->id,
							'type_name' 	=> $contact_type->name,
						);
					}
				}
			}
		}
		return $this;
				
	}

	function check_signature()
	{
		$params = &$this->params;
		$errors = &$this->errors;
		$object = &$this->object;
		$category = &$this->category;
		$user = &$this->user;

		

		if ($this->is_just_triggers($params))
			@list($values, $list_ids) = (array) Object_Utils::get_parsed_parameters(NULL, $object->id, TRUE);
		else 
			@list($values, $list_ids) = (array) Object_Utils::get_parsed_parameters($params, NULL, TRUE);

		$this->signature 				= ($this->is_union_enabled()) ?
													$this->generate_signature("", "",$values) : 
														$this->generate_signature($params->title_adv, $params->user_text_adv, $values);

		$this->signature_full = $this->generate_signature($params->title_adv, $params->user_text_adv, $values);														

		$this->options_exlusive_union 	= $this->get_options_exlusive_union($params->address, $params->city_id, $category->id, $list_ids);

		if ($this->is_similarity_enabled())
		{
			$max_similarity = Kohana::$config->load('common.max_object_similarity');
			$similarity 	= ORM::factory('Object_Signature')->get_similarity($max_similarity,$this->signature_full, NULL, $params->object_id, $user->id, "_full");
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

	function check_signature_for_union()
	{
		$params = &$this->params;
		$errors = &$this->errors;
		$category = &$this->category;
		$user = &$this->user;

		if ($this->is_union_enabled() AND $this->is_union_enabled_by_category($category->id) AND !$this->union_cancel)
		{
			$max_similarity = Kohana::$config->load('common.max_object_similarity');
			$similarity 	= ORM::factory('Object_Signature')->get_similarity($max_similarity, $this->signature, $this->options_exlusive_union, $params->object_id);

			if ($similarity["sm"] > $max_similarity){

				$parent_id = (int) ORM::factory('Object', $similarity["object_id"])->parent_id;
				if ($parent_id == 0)
				{
					$this->create_union = TRUE;
					$this->object_without_parent_id = $similarity["object_id"];
				} else {
					$this->edit_union = TRUE;
					$this->parent_id = $parent_id;
				}				
			}
		}

		return $this;
	}

	function save_union()
	{
		$params = &$this->params;
		$object = &$this->object;
		$errors = &$this->errors;
		$category = &$this->category;
		$user = &$this->user;

		if ($this->is_union_enabled() AND $this->is_union_enabled_by_category($category->id))
		{ 
			$parent_id = 0;

			if ($this->create_union OR $this->edit_union)
			{
				//ry {
					$this->original_params["object_id"] = $this->parent_id;
					$this->original_params["rubricid"]  = $category->id;
					$this->original_params["city_id"]   = $object->city_id;

					$objects_for_union = Array(
							"initial_object" 		 => $this->object_without_parent_id,
							"current_object_source"  => $object->id
						);

					$parent_id = (int) Object::PlacementAds_Union($this->original_params, $objects_for_union, $this->edit_union);
				//}
				//catch (Exception $e) {
				// 	$errors['union_error'] = $e->getMessage();
				//}
			} 

			if ($parent_id >0 )	
				$this->parent_id = $parent_id;

		}		

		return $this;

	}

	function init_validation_rules_for_attributes()
	{
		$form_references = &$this->form_references;
		$conditions = &$this->conditions;
		foreach ($form_references as $reference)
		{
			if (in_array($reference->id, $conditions->as_array('id', 'for_reference')) AND ! $this->is_shown($conditions, $reference->id))
			{
				// если атрибут есть в уловиях, но не показывается - пропускаем
				continue;
			}

			$rules = array();

			if ($reference->is_range)
			{
				$params = array(
					'param_'.$reference->id.'_min',
					'param_'.$reference->id.'_max',
				);
			}
			else
			{
				$params = array('param_'.$reference->id);
			}

			if ($reference->is_required)
			{
				$rules[] = array('not_empty', array(':value', $reference->attribute_obj->title));
			}

			switch ($reference->attribute_obj->type)
			{
				case 'integer':
					$rules[] = array('digit');
					$rules[] = array('not_0', array(':value', $reference->attribute_obj->title));
					$rules[] = array('max_length', array(':value', $reference->attribute_obj->solid_size));
				break;

				case 'numeric':
					$rules[] = array('numeric');
					$rules[] = array('max_length', array(':value', $reference->attribute_obj->solid_size+$reference->attribute_obj->frac_size+1));
				break;

				case 'text':
					$rules[] = array('max_length', array(':value', $reference->attribute_obj->max_text_length));
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
		$errors = &$this->errors;
		$user = &$this->user;
		//заполнены ли обязательные параметры
		if ( ! $this->validation->check())
		{
			$errors = $this->validation->errors('validation/object_form');
		}

		// указаны ли контакты
		if ( ! count($this->contacts))
		{
			$errors['contacts'] = Kohana::message('validation/object_form', 'empty_contacts');
		}

		// проверяем количество уже поданных пользователем объявлений
		if ( ! $this->category->check_max_user_objects($user, $this->params->object_id))
		{
			$errors['contacts'] = Kohana::message('validation/object_form', 'max_objects');
		}

		return $this;
	}

	function save_city_and_addrress()
	{
		$params = &$this->params;
		$city = &$this->city;
		$location = &$this->location;
		$object = &$this->object;

		// сохраняем город если нет такого города в базе
		$city = Kladr::save_city($params->city_kladr_id, $params->city_name);

		@list($lat, $lon) = explode(',', $params->object_coordinates);

		$location = Kladr::save_address($lat, $lon,
 				$params->address,
 				$params->city_kladr_id,
 				$params->address_kladr_id
 			);

		// если не нашли адрес, то берем location города
		if ( ! $location->loaded())
		{
			$location = $city->location;
		}

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
		$object->user_text 			= $params->user_text_adv;
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
			if (!empty($this->parent_id))
			{
				$object->parent_id = $this->parent_id;
			}
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
			// @todo вынести максимальное количество фотографий в конфиг
			$userphotos = array_slice($userphotos, 0, 8);
			$main_photo = $params->active_userfile;
			if ( ! $main_photo AND isset($userphotos[0]))
			{
				$main_photo = $userphotos[0];
			}

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
			$attachment = ORM::factory('Object_Attachment');
			$attachment->filename 	= $params->video ;
			$attachment->type 		= $params->video_type;
			$attachment->object_id 	= $object->id;
			$attachment->save();
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
		// отключаем комментарии к объявлению
		if ($params->block_comments)
		{
			$object->disable_comments();
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
						->cached(Date::DAY)
						->find();
				if ( $action->loaded() )
				{
					$object->action = $action->action_id;
				}
			}

			$form_element = ORM::factory('Form_Element')
				->with('reference_obj:attribute_obj')
				->where('form_element.reference', '=', $reference_id)
				->cached(Date::DAY)
				->find();

			if ( ! $form_element->loaded())
			{
				// неизвестный элемент формы
				continue;
			}

			if ($form_element->reference_obj->attribute_obj->type == 'boolean' AND ! $boolean_deleted)
			{
				ORM::factory('Data_Boolean')->where('object', '=', $object->id)->delete_all();
				$boolean_deleted = TRUE;
			}

			// удаляем старые значения
			ORM::factory('Data_'.Text::ucfirst($form_element->reference_obj->attribute_obj->type))
				->where('object', '=', $object->id)
				->where('reference', '=', $form_element->reference_obj->id)
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
				if (!$form_element->reference_obj->attribute_obj->is_price)
					continue;
			}

			// сохраняем цену для объявления
			if ($form_element->reference_obj->attribute_obj->is_price)
			{
				if (is_array($value) and isset($value['min']))
				{
					$object->price = $value['min'];
				}
				else
				{
					$object->price = $value;
				}

				$object->price_unit = $form_element->reference_obj->attribute_obj->unit;
			}

			// сохраняем дата атрибут
			$data = ORM::factory('Data_'.Text::ucfirst($form_element->reference_obj->attribute_obj->type));
			$data->attribute 	= $form_element->reference_obj->attribute;
			$data->object 		= $object->id;
			$data->reference 	= $form_element->reference_obj->id;
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
				foreach ($value as $value_detail) 
				{
					$data2 = clone $data;
					$data2->value = (int)$value_detail;
					$data2->save();
				}
			else
				$data->save();
		}
		return $this;
	}

	function save_generated()
	{
		$object = &$this->object;

		if ($object->category_obj->title_auto_fill)
		{
			$object->title = $object->generate_title();
		}

		$object->full_text = $object->generate_full_text();
		$object->save();
		return $this;
	}

	function send_external_integrations()
	{
		$object = &$this->object;
		// сохраняем запись для короткого урла *.ya24.biz
		$object->send_to_db_dns();

		if ( ! $this->is_edit) 
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
			$subj = $this->is_edit 
				? 'Вы изменили Ваше объявление. Теперь объявление выглядит так:' 
				: 'Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!';

			$msg = View::factory('emails/add_notice',
					array('h1' => $subj,'object' => $object, 'name' => $user->get_user_name(), 
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
			$user->add_verified_contact($contact['type'], $contact['value']);

			// сохраянем новые контакты для объявления
			$object->add_contact($contact['type'], $contact['value']);
		}

		return $this;
	}

	function init_defaults()
	{
		/*$this->params = new stdClass();
		$this->params->object_id = NULL;
		$this->params->rubricid = NULL;
		$this->params->session_id = NULL;
		$this->params->title_adv = NULL;
		$this->params->user_text_adv = NULL;
		$this->params->default_action = NULL;
		$this->params->contact = NULL;
		$this->params->lifetime = NULL;
		$this->params->from_company = NULL;
		$this->params->address_kladr_id = NULL;
		$this->params->object_coordinates = NULL; 
		$this->params->address = NULL;
		$this->params->city_kladr_id = NULL;
		$this->params->city_name = NULL;
		$this->params->address_str = NULL;
		$this->params->userfile = NULL;
		$this->params->active_userfile = NULL;
		$this->params->video = NULL;
		$this->params->video_type = NULL;
		$this->params->block_comments = NULL;		
		$this->params->parent_id = NULL;*/

		$this->contacts = array();
	}

	private static function raise_error($text){
		throw new HTTP_Exception_404($text);		
	}

	private function is_shown($conditions, $reference_id)
	{
		$shown = FALSE;

		foreach ($conditions as $condition)
		{
			if ($reference_id == $condition->for_reference)
			{
				if (property_exists($this->params, 'param_'.$condition->reference)) 
				{
					$params = explode(',', $this->params->{'param_'.$condition->reference});
					foreach ($params as $param)
					{
						if ($param == $condition->value_list OR $param == $condition->value_boolean)
						{
							$shown = TRUE;
						}
					}
				}
			}
		}
		
		return $shown;
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

	private static function get_options_exlusive_union($address, $city_id, $category_id, $list_ids = Array())
	{
		$return = Array();
		if ($address){
			$address_signature = Object_Utils::generate_signature( $address );
			array_merge($return, $address_signature);
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
					$return[] = $item;
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
