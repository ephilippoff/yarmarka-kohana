<?php defined('SYSPATH') or die('No direct script access.');


class Task_Objectload extends Minion_Task
{
	const SETTING_NAME = "massload_enable";

	protected $_options = array(
		"user_id"  => NULL,
		"objectload_id"  => NULL,
		"filter"  => NULL,
		"test"  => FALSE, //проверка на ошибки, без сохранения объявлений
		"publish_unpublish" => FALSE
	);

	protected function _execute(array $params)
	{
		$user_id 			= $params['user_id'];
		$publish_unpublish 			= $params['publish_unpublish'];

		if ($publish_unpublish) {
			$this->publish_unpublish($params["objectload_id"]);
			return;
		}

		$ct = ORM::factory('Crontask')->begin("Objectload", $params);

		
			if ($user_id)
			{
				try {
					$this->load($params, $ct);
				} catch (Exception $e)
				{
					$ct->error($e->getMessage());
					Minion::write("Error", $e->getMessage());
					return;
				}
			} 
				else 
			{
				$user_settings = ORM::factory('User_Settings')
										->where("name","=",self::SETTING_NAME)
										->order_by("id","asc")->find_all();
				foreach ($user_settings as $setting)
				{
					$ct->_update();
					if (!$ct->_check($ct->id))
						break;

					$params["user_id"] = $setting->user_id;
					try {
						$this->load($params, $ct);
					} catch (Exception $e)
					{
						//$ct->error($e->getMessage());
						Minion::write("Error", $e->getMessage());
						continue;
					}
				}
			}
		
		$ct->end();
	}

	/*
		objectload_id = <id> - loading objectload by id, if "last" - loading last objectload
		filter = notloaded=1,witherror=1,category=flat_resale

	*/
	function load(array $params, &$ct)
	{

		$user_id 			= $params['user_id'];
		$filter 			= $params['filter'];
		$objectload_id 		= $params['objectload_id'];
		$test 				= $params['test'];

		$filters = new Obj();
		if ($filter)
		{
			foreach (explode(",", $filter) as $f)
			{
				@list($key,$value) = explode("=", $f);
				$filters->{$key} = $value;
			}
		}

		if (!$user_id)
		{
			Minion::write("Error", "User is not defined");
			return;
		}

		$user 		=  ORM::factory('User', $user_id);
		Minion::write("Success", "User :".$user->org_name." ".$user->email." (".$user_id.")");
		Auth::instance()->force_login($user);
		$db = Database::instance();

		$set_no_company = FALSE;

		$user_settings = ORM::factory('User_Settings')
								->where("name","=","set_no_company")
								->where("user_id","=",$user_id)
								->order_by("id","asc")->find();

		if ($user_settings->loaded()) {
			$set_no_company = TRUE;
		}

		$ol = new Objectload($user_id, $objectload_id);
		$ol->setState(4);
		$ol->loadSettings($user_id);

		if ($filters->category AND !array_key_exists($filters->category, $ol->_settings["configs"]))
		{
			Minion::write("Error", "This category is not defined");
			return;
		}

		if (!$objectload_id)
		{
			try {
				$ol->downloadLinks();
			} catch (Exception $e)
			{
				if ($ol->_objectload_id) {
					$_ol = ORM::factory('Objectload', $ol->_objectload_id);
						if ($_ol->loaded()) {
							$_ol->set_state(99, "Ошибка при загрузке файла");
						}
				}
				Minion::write("Error", "link filed");
				throw $e;
			}
			Minion::write("Success", "Links loaded");

			try {

				$db->begin();
				Minion::write("Success", "Saving to temp tables ...");
				$ol->saveTempRecordsByLoadedFiles();
				Minion::write("Success", "Records saved");

				$db->commit();
			} catch (Exception $e)
			{
				$db->rollback();
				Minion::write("Error", "Failed saveTempRecordsByLoadedFiles");
				throw $e;
			}

			
		}

		Minion::write("Success", "Start...");

		$ol->forEachRecord($filters, function($row, $category, $cc) use ($ol, $ct, $test, $set_no_company){
			
			$ct->_update();
			if (!$ct->_check($ct->id))
				return 'break';

			
			$prefix_log = $category."|".$cc->common."-".$cc->counter."/".$cc->count;
			$config = &$ol->_settings["configs"][$category];
			$dictionary = &$ol->_settings["dict_".$category];

			$validation = Massload::init_validation($row, $row->external_id, $config, $dictionary);

			if (!$validation->check()) {	
				$validation_errors = $validation->errors('validation/massload');
				Minion::write($prefix_log, "Error :".join("|", array_values($validation_errors)));
				return array(
						"status" 	=> "error",
						"text_error" => join("|", array_values($validation_errors))
					);	
			}

			if ($test)
				return 'continue';

			$row->set_no_company = $set_no_company;
			
			$object = new Obj( $ol->saveRowAsObject($row, $config, $dictionary) );

			if ($object->object_id) {

				$_photos = $ol->savePhotos($object->object_id, $row->images, FALSE);
				if (count($_photos) > 0) {
					$ol->saveMainPhoto($object->object_id);
				}

				Object_Compile::saveImagesToCompiled(ORM::factory('Object', $object->object_id));
				
			}

			Minion::write($prefix_log, $object->get_normal_string());

			$result = array("status" => "no");
			if ($object->error AND array_key_exists("nochange", $object->error))
				$result = array(
						"status" 	=> "nochange",
						"object_id" => $object->object_id
					);
			elseif ($object->error)
				$result = array(
						"status" 	=> "error",
						"text_error" => "(Ошибка стр.".$object->external_id.") ".join("|", array_values($object->error))
					);
			else
				$result = array(
							"status" => "success",
							"edit" 	 => $object->is_edit,
							"object_id" => $object->object_id
						);

			if ( $row->premium AND in_array($result["status"], array("nochange", "success")) )
			{
				$isPremium = Task_Objectload::setPremium($object->object_id);
				if ($isPremium)
					Minion::write($prefix_log, 'Активировали премиум. Осталось: '.$isPremium);

			}
			return $result;

		});
		
		if (!$test)
		{
			$this->publish_unpublish( $ol->_objectload_id );
		}

		$ol->setState(5);
		
		ORM::factory('Objectload', $ol->_objectload_id)
			->update_statistic();

		if (!$test)
		{

			// $emails = $ol->sendReport();
			// if (count($emails)) {
			// 	Minion::write("Send report", Debug::vars($emails));
			// }
			
		}

		Minion::write("Success", 'End');
		return $ol->_objectload_id;
		//Temptable::delete_table($name);
	}


	static function setPremium($object_id)
	{
		if (!$object_id)
			return FALSE;

		$alreadyBuyed = Service_Premium::is_already_buyed($object_id);
		if (!$alreadyBuyed)
		{
			return Service_Premium::apply_prepayed($object_id);
		}

		return FALSE;
	}


	function publish_unpublish($objectload_id) {

		ORM::factory('Objectload', $objectload_id)
			->clear_doubles(function ($comment){
				Minion::write(1, 'Удаляем дубли '.$comment);
			});

		ORM::factory('Objectload', $objectload_id)
			->unpublish_expired(function ($comment, $category){
				Minion::write(2, 'Снимаем закончившиейся объявления '.$category.' '.$comment);
			});

		ORM::factory('Objectload', $objectload_id)
			->publish_and_prolonge(function ($comment){
				Minion::write(3,'Продляем и включаем активные объявления '.$comment);
			});
	}

	
}