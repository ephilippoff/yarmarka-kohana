<?php defined('SYSPATH') or die('No direct script access.');


class Task_Objectload extends Minion_Task
{
	protected $_options = array(
		"category" => NULL,
		"link"	   => NULL,
		"user_id"  => NULL,
		"objectload_id"  => NULL,
		"filter"  => NULL
	);

	/*
		objectload_id = <id> - loading objectload by id, if "last" - loading last objectload
		filter = notloaded=1,witherror=1,category=flat_resale

	*/
	protected function _execute(array $params)
	{
		$link 				= $params['link'];
		$user_id 			= $params['user_id'];
		$filter 			= $params['filter'];
		$objectload_id 		= $params['objectload_id'];

		$filters = new Obj();
		foreach (explode(",", $filter) as $f)
		{
			@list($key,$value) = explode("=", $f);
			$filters->{$key} = $value;
		}

		if (!$user_id)
		{
			Minion_CLI::write("User is not defined");
			return;
		}

		$user 		=  ORM::factory('User', $user_id);
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

			$db->begin();

			$ol->saveTempRecordsByLoadedFiles();
			Minion_CLI::write("Records saved");

			$db->commit();
		}

		Minion_CLI::write("Starting save Objects ...");
		$ol->forEachRecord($filters, function($row, $category, $cc) use ($ol){

			$prefix_log = Minion_CLI::color('['.$category."|".$cc->common."-".$cc->counter."/".$cc->count.']: ','yellow');
			
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

			$object = new Obj( $ol->saveRowAsObject($row, $config, $dictionary) );

			Minion_CLI::write($prefix_log.Debug::vars($object));

			if ($object->error)
				return array(
						"status" 	=> "error",
						"text_error" => "Строка external_id: ".join("|", array_values($object->error))
					);
			else
				return array(
							"status" => "success",
							"edit" 	 => $object->is_edit
						);

		});
		

		Minion_CLI::write('success');

		//Temptable::delete_table($name);
	}

}
