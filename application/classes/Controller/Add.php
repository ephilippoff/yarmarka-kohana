<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function action_save_object()
	{
		$this->auto_render = FALSE;

		$json = array();

		$user = Auth::instance()->get_user();

		if ( ! Request::current()->is_ajax())
		{
			throw new HTTP_Exception_404('only ajax requests allowed');
		}

		$is_edit = FALSE;

		// смотрим редактирование это или публикация
		if ($object_id = (int) $this->request->post('object_id'))
		{
			$object = ORM::factory('Object', $object_id);
			if ($object->loaded())
			{
				$is_edit = TRUE;
				if ( ! $user->can_edit_object($object->id))
				{
					throw new HTTP_Exception_404('user can\'t edit this ad');
				}
			}
			else
			{
				throw new HTTP_Exception_404('object not found');
			}
		}
		else
		{
			$object = ORM::factory('Object');
		}

		$errors = array();

		// объект валидации
		$validation = Validation::factory($this->request->post())
			->rule('city_kladr_id', 'not_empty')
			->rule('contact', 'not_empty');

		// если пользователь не аворизован, то добавляем валидацию для его полей
		if ( ! $user)
		{
			if ($this->request->post('new_email'))
			{
				$validation->rules('new_email', array(
					array('not_empty'),
					array('email'),
				))->rule('rules_confirmed', 'not_empty');
				// @todo add captcha validation
			}
			else
			{
				$validation->rules('email', array(
					array('not_empty'),
					array('email'),
				));
				$validation->rule('password', 'not_empty');
			}
		}

		// идентификатор сессии в CI
		$session_id = $this->request->post('session_id');

		// собираем контакты
		$contacts = array();
		array_walk($_POST, function($value, $key) use (&$contacts, $session_id){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$value = trim($_POST['contact_'.$matches[1].'_value']);
				if ($value)
				{
					$contact_type 	= ORM::factory('Contact_Type', $_POST['contact_'.$matches[1].'_type']);
					$contact 		= ORM::factory('Contact')->by_contact_and_type($value, $contact_type->id)
						->find();
					if ($contact_type->loaded() AND $contact->is_verified($session_id))
					{
						$contacts[] = array(
							'value' => $value,
							'type' => $contact_type->id,
							'type_name' => $contact_type->name,
						);
					}
				}
			}
		});

		// категория объявления
		$category = ORM::factory('Category', $this->request->post('rubricid'));
		if ( ! $category->loaded())
		{
			throw new HTTP_Exception_404('undefined category');
		}

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
				array('max_length', array(':value', 1500)),
			));
		}

		$form_references = Forms::get_by_category_and_type($category->id, 'add');
		$conditions = Forms::get_category_conditions($category->id);
		
		// валидация для полей формы
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
				$validation->rules($param, $rules);
			}
		}

		// проверяем поля формы
		if ( ! $validation->check())
		{
			$errors = $validation->errors('validation/object_form');
		}

		// указаны ли контакты
		if ( ! count($contacts))
		{
			$errors['contacts'] = Kohana::message('validation/object_form', 'empty_contacts');
		}

		// если пользователь не авторизован
		if ( ! $user AND ! $errors)
		{
			if ($this->request->post('new_email'))
			{
				// регистрация нового пользователя
				try
				{
					$login = $email = $this->request->post('new_email');
					$random_password = Text::random();
					$user = User::register($login, $email, $random_password);
				}
				catch(ORM_Validation_Exception $e)
				{
					$user_errors = $e->errors('validation');
					if (isset($user_errors['email']))
					{
						$user_errors['new_email'] = $user_errors['email'];
						unset($user_errors['email']);
					}
					$errors += $user_errors;
				}
			}
			else
			{
				// авторизация пользователя
				try 
				{
					// Auth::instance()->login($this->request->post('email'), $this->request->post('password'));
					// $user = Auth::instance()->get_user();
					$user = Auth::instance()->check_user($this->request->post('email'), $this->request->post('password'));
					$json['user_token'] = Auth::instance()->create_token($user)->token;
				}
				catch(Exception $e)
				{
					$errors['email'] = $e->getMessage();

					// если пользователь не активировал учетную запись
					if ($e->getCode() == 300)
					{
						$errors['email'] .= "<div class=\"errors\">
							<br />
							<a href=\"#\" onClick=\"send_activation_mail('{$this->request->post('email')}', this);return false;\">Отправить письмо о потдверждении регистрации еще раз</a>
						</div>";
					}
				}
			}
		}

		// проверяем количество уже поданных пользователем объявлений
		if ( ! $category->check_max_user_objects($user, $object_id))
		{
			$errors['contacts'] = Kohana::message('validation/object_form', 'max_objects');
		}

		if ( ! $errors)
		{
			// время жизни объявления
			switch ($this->request->post('lifetime')) 
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

			// сохраняем город если нет
			$city = Kladr::save_city($this->request->post('city_kladr_id'), $this->request->post('city_name'));

			// сохраняем адрес
			$location = Kladr::save_address($this->request->post('address_kladr_id'), 
				$this->request->post('object_coordinates'), 
				$this->request->post('address'),
				$this->request->post('city_kladr_id')
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
								$cities[$key] = $city->id;
							}
						}

						$object->cities = $cities;
					}
				}
			}

			if ($this->request->post('default_action'))
			{
				$object->action 		= $this->request->post('default_action');
			}
			$object->category 			= $category->id;
			$object->contact 			= $this->request->post('contact');
			$object->city_id			= $city->id;
			$object->ip_addr 			= Request::$client_ip;
			
			if ($is_edit) // если это редактирвоание, то is_published не трогаем
			{
				$object->is_published 	= $user->loaded() ? 1 : 0;
			}

			if ( ! $category->title_auto_fill)
			{
				$object->title 			= $this->request->post('title_adv');
			}

			if ($this->request->post('from_company') AND $user->linked_to->loaded())
			{
				$object->author_company_id = $user->linked_to->id;
			}
			else
			{
				$object->author_company_id = $user->id; //DB::expr('NULL');
			}

			$object->author 			= $user->id;
			$object->user_text 			= $this->request->post('user_text_adv');
			$object->date_expiration	= $date_expiration;
			$object->geo_loc 			= $location->get_lon_lat_str();
			$object->location_id		= $location->id;

			// сохраняем объявление
			$object = Object::save($object, $this->request);

			if ($is_edit)
			{	
				// удаляем связи на старые контакты
				$object->delete_contacts();
			}

			// сохраянем новые контакты
			foreach ($contacts as $contact)
			{
				$object->add_contact($contact['type'], $contact['value']);
			}

			if ($object->is_bad === 1)
			{
				// если объявление было на исправлении, то отправляем на модерацию
				$object->to_forced_moderation();
			}

			// сохраняем запись для короткого урла *.ya24.biz
			$object->send_to_db_dns();

			if ( ! $is_edit) 
			{
				//пишем id объявления во временную таблицу для последующего обмена с terrasoft
				$object->send_to_terrasoft();
			}

			// отправляем письмо пользователю, если была быстрая регистрация
			if ( ! empty($random_password))
			{
				$msg = View::factory('emails/fast_register_success', 
					array('activation_code' => $user->code, 'Password' => $random_password, 'object_id' => $object->id))
					->render();

				Email::send($user->email, Kohana::$config->load('email.default_from'), 'Подтверждение регистрации на “Ярмарка-онлайн”', $msg);
			}

			// @todo тут должно идти сохранение контактов

			// отправляем уведомление о успешном редактировании/публикации
			$subj = $is_edit 
				? 'Вы изменили Ваше объявление. Теперь объявление выглядит так:' 
				: 'Поздравляем Вас с успешным размещением объявления на «Ярмарка-онлайн»!';

			$msg = View::factory('emails/add_notice',
					array('h1' => $subj,'object' => $object, 'name' => $user->get_user_name(), 
						'obj' => $object, 'city' => $city, 'category' => $category, 'subdomain' => Region::get_domain_by_city($city->id), 
						'contacts' => $contacts, 'address' => $this->request->post('address_str')));

			Email::send($user->email, Kohana::$config->load('email.default_from'), $subj, $msg);

			$json['object_id'] = $object->id;
		}
		else
		{
			$json['error'] = $errors;
		}

		$this->response->body(json_encode($json));
	}

	/**
	 * Показан элемент на форме или нет
	 * 
	 * @param  array  $conditions
	 * @param  integer  $reference_id
	 * @return boolean
	 */
	public function is_shown($conditions, $reference_id)
	{
		$shown = FALSE;

		foreach ($conditions as $condition)
		{
			if ($reference_id == $condition->for_reference)
			{
				$params = explode(',', $this->request->post('param_'.$condition->reference));
				foreach ($params as $param)
				{
					if ($param == $condition->value_list OR $param == $condition->value_boolean)
					{
						$shown = TRUE;
					}
				}
			}
		}
		
		return $shown;
	}
}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */