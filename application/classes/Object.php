<?php defined('SYSPATH') OR die('No direct script access.');

class Object
{
	public static function save(Model_Object $object, Request $request)
	{
		$db = Database::instance();

		try
		{
			// start transaction
			$db->begin();

			// сохраняем object чтобы получить object_id
			$object->save();

			// удаляем старые аттачи
			// @todo по сути не надо заного прикреплять те же фотки при редактировании объявления
			ORM::factory('Object_Attachment')->where('object_id', '=', $object->id)->delete_all();

			// собираем аттачи
			if ($userphotos = $request->post('userfile') AND is_array($userphotos))
			{
				// @todo вынести максимальное количество фотографий в конфиг
				$userphotos = array_slice($userphotos, 0, 8);
				$main_photo = $request->post('active_userfile');
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

			// сохраняем видео
			if ($request->post('video') AND $request->post('video_type'))
			{
				$attachment = ORM::factory('Object_Attachment');
				$attachment->filename 	= $request->post('video');
				$attachment->type 		= $request->post('video_type');
				$attachment->object_id 	= $object->id;
				$attachment->save();
			}

			// отключаем комментарии к объявлению
			if ($request->post('block_comments'))
			{
				$object->disable_comments();
			}

			$params = Object::get_form_elements_from_post($request->post());

			$boolean_deleted = FALSE; // если меняются булевые параметры, то удаляем все что есть в базе
			foreach ($params as $reference_id => $value)
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
				if (is_array($value) AND empty($value['min']) AND empty($value['max']) AND !isset($value[0]))
				{
					continue;
				}
				elseif (empty($value) or empty($value[0]))
				{
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

			if ($object->category_obj->title_auto_fill)
			{
				$object->title = $object->generate_title();
			}

			$object->full_text = $object->generate_full_text();
			$object->save();

			$db->commit();
		}
		catch(Exception $e)
		{
			Kohana::$log->add(Log::ERROR, 'Ошибка при сохранении объявления:'.$e->getMessage());
			Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибка при сохранении объявления', $e->getMessage());

			$db->rollback();

			throw $e;
		}

		return $object;
	}

	/**
	 * Get references id and form attribute values from _POST array
	 * 
	 * @param  array $post
	 * @return array
	 */
	public static function get_form_elements_from_post($post)
	{
		$params = array();
		foreach ($post as $key => $value)
		{
			if (preg_match('/param_([0-9]*)[_]{0,1}(.*)/', $key, $matches))
			{
				$reference_id = $matches[1];
				$postfix = $matches[2]; // max/min

				if ($postfix)
				{
					$params[$reference_id][$postfix] = trim($value);
				}
				else
				{	//Если несколько значений(is_multiple)
					if (is_array($value))
						//Организовываем подмассив
						foreach ($value as $one_value) 
							$params[$reference_id][] = $one_value;					
					else
						$params[$reference_id] = trim($value);
				}
			}
		}
		
		return $params;
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Object.php */