<?php defined('SYSPATH') or die('No direct script access.');


class Task_Objectload extends Minion_Task
{
	const SETTING_NAME = "massload_enable";

	protected $_options = array(
		"user_id"  => NULL,
		"objectload_id"  => NULL,
		"filter"  => NULL,
		"test"  => FALSE //проверка на ошибки, без сохранения объявлений
	);

	protected function _execute(array $params)
	{
		$user_id 			= $params['user_id'];

		$ct = ORM::factory('Crontask')->begin("Objectload", $params);

		try {
			if ($user_id)
			{
				$this->load($params, $ct);
			} 
				else 
			{
				$user_settings = ORM::factory('User_Settings')
										->where("name","=",self::SETTING_NAME)
										->order_by("id","desc")->find_all();
				foreach ($user_settings as $setting)
				{
					$ct->_update();
					if (!$ct->_check($ct->id))
						break;

					$params["user_id"] = $setting->user_id;
					$this->load($params, $ct);
				}
			}
		} catch (Exception $e)
		{
			$ct->error($e->getMessage());
			Minion_CLI::write($e->getMessage());
			return;
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
			Minion_CLI::write("User is not defined");
			return;
		}

		$user 		=  ORM::factory('User', $user_id);
		Minion_CLI::write("User :".$user->org_name." ".$user->email." (".$user_id.")");
		Auth::instance()->force_login($user);
		$db = Database::instance();

		$ol = new Objectload($user_id, $objectload_id);
		
		$ol->loadSettings($user_id);

		if ($filters->category AND !array_key_exists($filters->category, $ol->_settings["configs"]))
		{
			Minion_CLI::write("This category is not defined");
			return;
		}

		if (!$objectload_id)
		{
			$ol->downloadLinks();
			Minion_CLI::write("Links loaded");

			try {

				$db->begin();
				Minion_CLI::write("Saving to temp tables ...");
				$ol->saveTempRecordsByLoadedFiles();
				Minion_CLI::write("Records saved");

				$db->commit();
			} catch (Exception $e)
			{
				$db->rollback();
				throw $e;
			}

			
		}

		Minion_CLI::write("Start...");

		$ol->forEachRecord($filters, function($row, $category, $cc) use ($ol, $ct, $test){

			$ct->_update();
			if (!$ct->_check($ct->id))
				return 'break';

			$prefix_log = '['.$category."|".$cc->common."-".$cc->counter."/".$cc->count.']: ';
			
			$config = &$ol->_settings["configs"][$category];
			$dictionary = &$ol->_settings["dict_".$category];

			$validation = Massload::init_validation($row, $row->external_id, $config, $dictionary);

			if (!$validation->check()) {	
				$validation_errors = $validation->errors('validation/massload');
				Minion_CLI::write($prefix_log."Error :".join("|", array_values($validation_errors)));
				return array(
						"status" 	=> "error",
						"text_error" => join("|", array_values($validation_errors))
					);	
			}

			if ($test)
				return 'continue';

			$object = new Obj( $ol->saveRowAsObject($row, $config, $dictionary) );

			Minion_CLI::write($prefix_log.Debug::vars($object));

			if ($object->error)
				return array(
						"status" 	=> "error",
						"text_error" => "(Ошибка стр.".$object->external_id.") ".join("|", array_values($object->error))
					);
			else
				return array(
							"status" => "success",
							"edit" 	 => $object->is_edit
						);

		});
		
		ORM::factory('Objectload', $ol->_objectload_id)
			->update_statistic();

		Minion_CLI::write('End');

		//Temptable::delete_table($name);
	}

}
