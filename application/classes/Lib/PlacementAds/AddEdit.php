<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEdit {
	public $user;
	public $errors;
	public $params;
	public $validation;
	public $contacts;
	public $city;
	public $location;

	function Lib_PlacementAds_AddEdit()
	{
		$this->init_defaults();
	}

	function init_input_params($params)
	{
		if (is_array($params))
		{
			foreach($params as $key=>$value)
			{
				$this->params->{$key} = $value;	
			}
		}
		return $this;
	}

	function check_neccesaries()
	{
		$params = &$this->params;
		if ($this->is_edit != TRUE)
		{
			if (! $params->rubricid){
				$this->raise_error('undefined category');
			}
		}
		return $this;
	}

	function init_instances()
	{
		$params = &$this->params;

		$this->user = Auth::instance()->get_user();	

		if ($params->rubricid) 
		{
			$this->category = ORM::factory('Category', $params->rubricid);

			if ( ! $this->category->loaded() )
			{
				$this->raise_error('undefined category');
			}
		} 
		else 
		{
			$this->raise_error('undefined category');	
		}

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
				if ( ! $user->can_edit_object($object->id))
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
			//->rule('city_kladr_id', 'not_empty')
			->rule('contact', 'not_empty')
			->rule('email', 'email');	

		if ( ! $category->title_auto_fill)
		{
			$validation->rules('title_adv', array(
				array('not_empty'),
				array('min_length', array(':value', 15)),
			));
		}

		if ($category->address_required)
		{
			$validation->rule('address', 'not_empty');
		}

		if ($category->text_required)
		{
			$validation->rules('user_text_adv', array(
				array('not_empty'),
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
				$rules[] = array('not_empty');
			}

			switch ($reference->attribute_obj->type)
			{
				case 'integer':
					$rules[] = array('digit');
					$rules[] = array('not_0');
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

		// сохраняем адрес
		$location = Kladr::save_address(
			$params->address_kladr_id, 
			$params->object_coordinates, 
			$params->address,
			$params->city_kladr_id
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
		$object->date_expiration	= $this->lifetime_to_date($params->lifetime);
		$object->geo_loc 			= $location->get_lat_lon_str();
		$object->location_id		= $location->id;

		return $this;
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

		$attributes = $this->get_form_elements_from_params((array) $params);

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

		if ( ! $is_edit) 
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

	private function init_defaults()
	{
		$this->params = new stdClass();
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

		$this->contacts = array();
	}

	private function raise_error($text){
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

	private function lifetime_to_date($lifetime)
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

	private function get_form_elements_from_params($params)
	{
		$result = array();
		foreach ($params as $key => $value)
		{
			if (preg_match('/param_([0-9]*)[_]{0,1}(.*)/', $key, $matches))
			{
				$reference_id = $matches[1];
				$postfix = $matches[2]; // max/min

				if ($postfix)
				{
					$result[$reference_id][$postfix] = trim($value);
				}
				else
				{	//Если несколько значений(is_multiple)
					if (is_array($value))
						//Организовываем подмассив
						foreach ($value as $one_value) 
							$result[$reference_id][] = $one_value;					
					else
						$result[$reference_id] = trim($value);
				}
			}
		}
		
		return $result;
	}

}
