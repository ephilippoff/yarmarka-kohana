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
			->init_contacts()
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
				Kohana::$log->add(Log::ERROR, 'Default Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Default Ошибка при сохранении объявления', $e->getMessage());

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
			->init_contacts()
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
				Kohana::$log->add(Log::ERROR, 'Local Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Local Ошибка при сохранении объявления', $e->getMessage());

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

	static function PlacementAds_ByModerator($input_params)
	{
		$json = array();
		
		$add = new Lib_PlacementAds_AddEditByModerator();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->init_validation_rules()
			//->init_validation_rules_for_attributes()
			->init_contacts()
			->exec_validation();

		if ( ! $add->errors)
		{
			$add->save_city_and_addrress()
				->prepare_object()
				->save_typetr_object();

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
				Kohana::$log->add(Log::ERROR, 'ByModerator Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'ByModerator Ошибка при сохранении объявления', $e->getMessage());

				$db->rollback();

				throw $e;
			}

			$add->send_external_integrations();

			$json['object_id'] = $add->object->id;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function PlacementAds_ByMassLoad($input_params)
	{
		$json = array();
		
		$add = new Lib_PlacementAds_AddEditByMassLoad();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->init_validation_rules()
			//->init_validation_rules_for_attributes()
			->init_contacts()
			->exec_validation()
			->check_signature()
			->check_signature_for_union();

		if ( ! $add->errors)
		{
			$add->save_city_and_addrress()
				->prepare_object()
				->save_parentid_object();

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
					->save_contacts()
					->save_signature()
					->save_union();

				$db->commit();
			}
			catch(Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'MassLoad Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'MassLoad Ошибка при сохранении объявления', $e->getMessage());

				$db->rollback();

				throw $e;
			}

			$add//->send_external_integrations()
				->send_to_forced_moderation()
				->send_message();

			$json['object_id'] = $add->object->id;
			$json['parent_id'] = $add->parent_id;
			$json['error'] = $add->errors;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function PlacementAds_Union($input_params, $objects_for_union, $edit = FALSE)
	{

		$json = array();
		
		$add = new Lib_PlacementAds_AddUnion($objects_for_union);
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries();

		if (!$edit) {
			$add->prepare_object();
			$add->save_object();
		}

		$add->copy_photo()
			->copy_attributes();

		$add->update_union_objects()
			->save_aditional_info();

		

		return $add->object->id;

	}

	static function PlacementAds_JustRunTriggers($input_params)
	{		
		$json = array();

		$add = new Lib_PlacementAds_AddEdit();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_signature()
			->check_signature_for_union();

		if ((int) $add->$object->is_union >0)
			return $json;

		if ( ! $add->errors)
		{
			$add->save_parentid_object();

			$db = Database::instance();

			try
			{
				// start transaction
				$db->begin();

				$add->save_object()
					->save_signature()
					->save_union();

				$db->commit();
			}
			catch(Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'JustRunTriggers Ошибка при сохранении объявления:'.$e->getMessage());
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'JustRunTriggers Ошибка при сохранении объявления', $e->getMessage());

				$db->rollback();

				throw $e;
			}

			$json['object_id'] = $add->object->id;
			$json['parent_id'] = $add->parent_id;
			$json['error'] = $add->errors;
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