<?php defined('SYSPATH') or die('No direct script access.');


class Task_Priceload extends Minion_Task
{

	protected $_options = array(
		"user_id"  => NULL,
		"priceload_id"  => NULL,
		"filter"  => NULL
	);

	protected function _execute(array $params)
	{
		$user_id 			= $params['user_id'];

		$ct = ORM::factory('Crontask')->begin("Priceload", $params);

		$this->load($params, $ct);

		$ct->end();

	}

	function load(array $params, &$ct)
	{
		$user_id 			= $params['user_id'];
		$filter 			= $params['filter'];
		$priceload_id 		= $params['priceload_id'];

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
		$settings = new Obj();

		$filename = 'temp1';
		$filepath = './uploads/111.xls';

		$filesize = filesize($filepath);

		if (!$filesize)
		{
			Minion::write("Error", "Filesize");
			return;
		}
		Minion::write("Success", "Filesize:".$filesize);
		$settings->file = array(
				'tmp_name' => $filepath,
				'size' => $filesize,
				'name' => $filename,
				'type' => mime_content_type($filepath),
			);
		$ol = new Priceload($user_id, $settings, $priceload_id);

		$ol->saveTempRecordsByLoadedFiles();

		Minion::write("Success", "Succes");
	}
}