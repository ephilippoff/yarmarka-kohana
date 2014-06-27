<?php defined('SYSPATH') OR die('No direct script access.');

class Object
{
	static function PlacementAds_Default($input_params)
	{
		$json = array();
		
		$add = new Lib_PlacementAds_AddEdit();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->init_validation_rules()
			->init_validation_rules_for_attributes()
			->init_contacts(TRUE)
			->exec_validation();

		if ( ! $add->errors)
		{
			$add->save_city_and_addrress()
				->prepare_object();

			$db = Database::instance();

			try
			{
				// start transaction
				$db->begin();

				$add->save_object()
					->save_photo()
					->save_video()
					->save_other_options()
					->save_attributes()
					->save_generated()
					->save_contacts();

				$db->commit();
			}
			catch(Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибка при сохранении объявления', $e->getMessage());

				$db->rollback();

				throw $e;
			}

			$add->send_external_integrations()
				->send_to_forced_moderation()
				->send_message();

			$json['object_id'] = $add->object->id;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function PlacementAds_Local($input_params)
	{
		$json = array();
		
		$add = new Lib_PlacementAds_AddEditLocal();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->init_validation_rules()
			//->init_validation_rules_for_attributes()
			->init_contacts(TRUE)
			->exec_validation();

		if ( ! $add->errors)
		{
			$add->save_city_and_addrress()
				->prepare_object();

			$db = Database::instance();

			try
			{
				// start transaction
				$db->begin();

				$add->save_object()
					->save_photo()
					->save_video()
					->save_other_options()
					->save_attributes()
					->save_generated()
					->save_contacts();

				$db->commit();
			}
			catch(Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибка при сохранении объявления', $e->getMessage());

				$db->rollback();

				throw $e;
			}

			$add//->send_external_integrations()
				->send_to_forced_moderation()
				->send_message();

			$json['object_id'] = $add->object->id;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Object.php */