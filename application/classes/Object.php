<?php defined('SYSPATH') OR die('No direct script access.');

class Object
{
	public static function save(Model_Object $object, Request $request)
	{
		Database::instance()->begin();

		try
		{
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
			}

			// удаляем аттачи из временой таблицы
			foreach ($userphotos as $file) 
			{
				ORM::factory('Tmp_Img')->where('name', '=', $file)->delete();
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

			$params = self::get_form_elements_from_post($request->post());

			$boolean_deleted = FALSE; // если меняются булевые параметры, то удаляем все что есть в базе
			foreach ($params as $reference_id => $value)
			{
				$reference = ORM::factory('Reference')
					->with('attribute_obj')
					->where('id', '=', $reference_id)
					->find();

				if ( ! $reference->loaded())
				{
					// неизвестный reference
					continue;
				}

				if ($reference->type == 'boolean' AND ! $boolean_deleted)
				{
					ORM::factory('Data_Boolean')->where('object_id', '=', $object->id)->delete_all();
					$boolean_deleted = TRUE;
				}

				// удаляем старые значения
				ORM::factory('Data_'.$reference->type)
					->where('object_id', '=', $object->id)
					->where('reference', '=', $reference->id)
					->delete_all();

				// проверяем есть ли значение
				if ($reference->attribute_obj->is_range)
				{
					if (empty($value['min']) AND empty($value['max']))
					{
						continue;
					}
				}
				else
				{
					if (empty($value))
					{
						continue;
					}
				}

				// сохраняем цену для объявления
				if ($reference->attribute_obj->is_price)
				{
					if (is_array($value))
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
				$data = ORM::factory('Data_'.$reference->type);
				$data->attribute 	= $reference->attribute;
				$data->object 		= $object->id;
				$data->reference 	= $reference->id;
				if ($reference->attribute_obj->is_range)
				{
					$data->value_min = $value['min'];
					$data->value_max = $value['max'];
				}
				else
				{
					$data->value = $value;
				}
				$data->save();
			}

			$object->update();
		}
		catch(Exception $e)
		{
			Kohana::$log->add(Log::ERROR, 'Ошибка при сохранении объявления:'.$e->getMessage());
			Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибка при сохранении объявления', $e->getMessage());
			Database::instance()->rollback();
		}

		Database::instance()->commit();

		return $object;
	}

	/**
	 * Get references id and form attribute values from _POST array
	 * 
	 * @param  array $post
	 * @return array
	 */
	public function get_form_elements_from_post($post)
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
				{
					$params[$reference_id] = trim($value);
				}
			}

		}

		return $params;
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Object.php */