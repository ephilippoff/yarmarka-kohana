<?php defined('SYSPATH') OR die('No direct script access.');

class Object
{
	static function default_save_object($params)
	{
		$json = array();
		$user = Auth::instance()->get_user();

		//если в локале работаем с подачей, ставим 1
		$is_local = Kohana::$config->load("common.is_local");
		if ( Acl::check("object.add.type") AND Arr::get($params, "user_type", "default") == "moderator")
		{
			//убрана проверка каких либо атрибутов
			//убрана проверка контактов
			//убрана проверка на максимальное количество объяв в рубрику
			$json = Object::PlacementAds_ByModerator($params, $is_local);
		} else {
			$json = Object::PlacementAds_Default($params, $is_local);
		}

		return $json;
	}

	static function PlacementAds_Default($input_params, $is_local = FALSE)
	{
		if(!Security::check($input_params['csrf'])){
			$json['error'] = array("Подпись не прошла проверку подлинности. Обновите страницу");
			return $json;
		}

		$json = array();
		$user = Auth::instance()->get_user();
		
		$add = new Lib_PlacementAds_AddEdit();
		$add->init_input_params($input_params);

		if (!$user)
			$add->login();		

		$add->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->normalize_attributes()
			->init_contacts()
			->init_validation_rules()
			->init_additional()
			->init_validation_rules_for_attributes()
			->exec_validation()
			->check_signature();

		if ( ! $add->errors)
		{
			$add->save_address()
				//->save_many_cities()
				->prepare_object();

			$db = Database::instance();

			try
			{
				// start transaction
				$db->begin();

				$add->save_object()
					->save_photo()
					->save_video()
					->save_price()
					->save_other_options()
					->save_attributes()
					->save_generated()
					->save_contacts()
					->save_signature()
					->save_additional()
					->save_compile_object();

				$db->commit();
			}
			catch(Exception $e)
			{
				$exception_message  = 'Default Ошибка при сохранении объявления: </br>';
				$exception_message .= 'message: '.($e->getMessage()).'</br>';
				$exception_message .= 'input_params: '.Debug::vars($input_params).'</br>';
				$exception_message .= 'stack: '.($e->getTraceAsString()).'</br>';
				
				Kohana::$log->add(Log::ERROR, $exception_message);
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Default Ошибка при сохранении объявления', $exception_message);

				$db->rollback();

				throw $e;
			}

			$add->send_to_forced_moderation();

			$add->send_message();
			if (!$is_local) {
				$add->send_external_integrations()
					->send_message();
			}

			$json['object_id'] = $add->object->id;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function PlacementAds_ByModerator($input_params, $is_local)
	{
		$json = array();
		$user = Auth::instance()->get_user();

		$add = new Lib_PlacementAds_AddEditByModerator();
		$add->init_input_params($input_params);

		if (!$user)
			$add->login();		
		$add->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->normalize_attributes()
			->init_contacts()
			->init_validation_rules()
			->init_additional()
			//->init_validation_rules_for_attributes()
			->exec_validation();

		if ( ! $add->errors)
		{
			$add->save_address()
				->prepare_object()
				->save_typetr_object()
				->save_dates_object();

			$db = Database::instance();

			try
			{
				// start transaction
				$db->begin();

				$add->save_object()
					->save_photo()
					->save_video()
					->save_price()
					->save_other_options()
					->save_attributes()
					->save_generated()
					->save_contacts()
					->save_additional()
					->save_compile_object();

				$db->commit();
			}
			catch(Exception $e)
			{
				$exception_message  = 'ByModerator Ошибка при сохранении объявления: </br>';
				$exception_message .= 'message: '.($e->getMessage()).'</br>';
				$exception_message .= 'input_params: '.Debug::vars($input_params).'</br>';
				$exception_message .= 'stack: '.($e->getTraceAsString()).'</br>';

				Kohana::$log->add(Log::ERROR, $exception_message);
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'ByModerator Ошибка при сохранении объявления', $exception_message);

				$db->rollback();

				throw $e;
			}

			$add->send_to_forced_moderation();

			if (!$is_local) {
				$add->send_external_integrations();
			}

			$json['object_id'] = $add->object->id;
		}
		else
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function PlacementAds_ByMassLoad($input_params, $user_id)
	{
		$json = array();

		$input_params["itis_massload"] = 1;
		$input_params["end_user_id"] = $user_id;
		
		$add = new Lib_PlacementAds_AddEditByMassLoad();
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->normalize_attributes()
			->init_contacts()
			->init_validation_rules()
			//->init_validation_rules_for_attributes()
			->exec_validation()
			->check_signature();

		if ( ! $add->errors)
		{
			$add->save_address()
				->prepare_object()
				->save_external_info()
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
					->save_compile_object();

				$db->commit();
			}
			catch(Exception $e)
			{
				$exception_message  = 'MassLoad Ошибка при сохранении объявления: </br>';
				$exception_message .= 'message: '.($e->getMessage()).'</br>';
				$exception_message .= 'input_params: '.Debug::vars($input_params).'</br>';
				$exception_message .= 'stack: '.($e->getTraceAsString()).'</br>';

				Kohana::$log->add(Log::ERROR, $exception_message);
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'MassLoad Ошибка при сохранении объявления', $exception_message);

				$db->rollback();

				throw $e;
			}

			//$add//->send_external_integrations()
				//->send_to_forced_moderation();
				//->send_message();

			$json['object_id'] = $add->object->id;
			$json['external_id'] = $input_params['external_id'];
			$json['parent_id'] = $add->parent_id;
			$json['is_edit'] = $add->is_edit;
			$json['error'] = $add->errors;
		}
		else
		{
			$json['object_id'] = $add->object->id;
			$json['error'] = $add->errors;
			$json['external_id'] = $input_params['external_id'];
		}

		return $json;
	}

	static function PlacementAds_JustRunTriggers($input_params)
	{		
		$json = array();

		if ( Acl::check("object.add.type") )
		{
			$add = new Lib_PlacementAds_AddEditByModerator();
		} else {
			$add = new Lib_PlacementAds_AddEdit();
		}
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_signature();

		if ((int) $add->object->is_union >0)
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
					->save_signature();

				$db->commit();
			}
			catch(Exception $e)
			{
				$exception_message  = 'JustRunTriggers Ошибка при сохранении объявления: </br>';
				$exception_message .= 'message: '.($e->getMessage()).'</br>';
				$exception_message .= 'input_params: '.Debug::vars($input_params).'</br>';
				$exception_message .= 'stack: '.($e->getTraceAsString()).'</br>';

				Kohana::$log->add(Log::ERROR, $exception_message);
				Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'JustRunTriggers Ошибка при сохранении объявления', $exception_message);

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

	static function PlacementAds_Validate($input_params, $return_object = FALSE)
	{		
		$json = array();

		$input_params["just_check"] = 1;

		if ( Acl::check("object.add.type") )
		{
			$add = new Lib_PlacementAds_AddEditByModerator();
		} else {
			$add = new Lib_PlacementAds_AddEdit();
		}
		$add->init_input_params($input_params)
			->init_instances()
			->init_object_and_mode()
			->check_neccesaries()
			->init_contacts()
			->init_validation_rules()
			->init_validation_rules_for_attributes()
			->exec_validation();

		if ( ! $add->errors){
			$json['error'] = null;
			if ($return_object)
				$json['add_obj'] = $add;
		} else 
		{
			$json['error'] = $add->errors;
		}

		return $json;
	}

	static function canEdit($input_params)
	{
		$check = self::PlacementAds_Validate($input_params, TRUE);
		$error = $check["error"];
		
		if (isset($check['add_obj']->object->moder_state) AND $check['add_obj']->object->moder_state  == 1) {
			if (!$error) {
				$error = array();
			}
			if ($check['add_obj']->object->is_bad  == 1) {
				$error["common"] = "Объявление было снято модератором. Исправьте объявление для того, чтобы его разместить (мы автоматически переведем вас на страницу редактирования) ";
			} elseif ($check['add_obj']->object->is_bad  == 2) {
				$error["common"] = "Объявление было заблокирвоано модератором окончательно.";
			}
			
		} 

		if ($error)
		{
			$errors = array_values($error);
			return Array("code" => "error",
						 "errors" => join(", ", $errors)
				);
		} else {
			$input_params['just_check'] = 1;//todo два раза генерятся параметры для объявы, не айс
			$trigger = self::PlacementAds_JustRunTriggers($input_params);
			$error = $trigger["error"];
			if (!array_key_exists("object_id", $trigger))
			{
				$errors = array_values($error);
				return Array("code" => "error",
						 	"errors" => join(", ", $errors)
						 );
			} else {
				return Array("code" => "ok", "parent_id" =>$trigger["parent_id"]);
			}
		}
	}
}

/* End of file Object.php */
/* Location: ./application/classes/Object.php */