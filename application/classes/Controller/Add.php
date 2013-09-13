<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function action_save_object()
	{
		// @todo
		$this->request->post('rubricid', 15);

		$this->auto_render = FALSE;

		$user = Auth::instance()->get_user();

		if ( ! Request::current()->is_ajax())
		{
			// throw new HTTP_Exception_404('only ajax requests allowed');
		}

		// смотрим редактирование это или публикация
		if ($object_id = (int) $this->request->post('object_id'))
		{
			$object = ORM::factory('Object', $object_id);
			if ($object->loaded())
			{
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

		// собираем контакты
		$contacts = array();
		array_walk($_POST, function($value, $key) use (&$contacts){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$contacts[] = array(
					'value' => $this->request->post('contact_'.$matches[1].'_value'),
					'type' => $this->request->post('contact_'.$matches[1].'_type'),
				);
			}
		});

		// ищем заблокированные среди контактов
		$blocked_contacts = array();
		foreach ($contacts as $contact)
		{
			$blocked_contact = ORM::factory('Contact_Block_List')->where('contact_type_id', '=', $contact['type'])
				->where('contact', '=', $contact['value'])
				->find();
			if ($blocked_contact->loaded())
			{
				$blocked_contacts[] = $blocked_contact->contact;
			}
		}
		
		// категория объявления
		$category = ORM::factory('Category', $this->request->post('rubricid'));
		if ( ! $category->loaded())
		{
			throw new HTTP_Exception_404('undefined category');
		}

		if ($category->title_auto_fill)
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
					$rules[] = array('max_length', array(':value', $reference->attribute_obj->solid_size));
					// @todo check not zero validation
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
			$errors = $validation->errors('object_form');
		}

		// указаны ли контакты
		if ( ! count($contacts))
		{
			$errors['contacts'] = Kohana::message('object_form', 'empty_contacts');
		}

		// проверяем заблоки
		if ($blocked_contacts)
		{
			$errors['contacts'] = strtr(Kohana::message('object_form', 'blocked_contacts'), array(':contacts' => implode(',', $blocked_contacts)));
		}

		// если пользователь не авторизован
		if ( ! $user)
		{
			if ($this->input->post('new_email'))
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
					$errors += $e->errors('user');
				}
			}
			else
			{
				// авторизация пользователя
				try 
				{
					Auth::instance()->login($this->request->post('email'), $this->request->post('password'));
					$user = Auth::instance()->get_user();
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
			$errors['contacts'] = Kohana::message('object_form', 'max_objects');
		}

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


		print_r($errors);
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