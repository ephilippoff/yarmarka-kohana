<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Objectload_Files extends ORM {

	protected $_table_name = 'objectload_files';

	function get_statistic_row($_statistic = NULL)
	{
		if (!$_statistic)
			return;

		$statstr = '';
			
		$statistic = new Obj(unserialize($_statistic));
		$new = $statistic->loaded - $statistic->edited;
		
		$flagend ='';
		if ($statistic->loaded + $statistic->error <> $statistic->all)
			$flagend = '<span style="color:red ;">(!)</span>';

		$errorstr ='';
		if($statistic->error>0)
			$errorstr = "<span style='color:red;'> ".$statistic->error."</span>";

		$statstr = $new." / ".$statistic->edited." / ".$statistic->nochange." / ".$errorstr." = ".$statistic->all." ".$flagend;

		return $statstr;
	}

	function get_statistic()
	{
		if (!$this->loaded())
			return;

		return unserialize($this->statistic);
	}

	function notloaded_records_exists($_statistic = NULL)
	{
		if (!$_statistic)
			return;

		$statistic = new Obj(unserialize($_statistic));

		if ($statistic->loaded + $statistic->error <> $statistic->all)
			return TRUE;
		else
			return FALSE;

	}

	function error_exists($_statistic = NULL)
	{
		if (!$_statistic)
			return;

		$statistic = new Obj(unserialize($_statistic));
		
		if ($statistic->error > 0)
			return TRUE;
		else
			return FALSE;
	}

	function get_objectload_files_list($objectload_id)
	{
		if (!$objectload_id)
			return;

		$objectload_files = array();

		$files = $this->where("objectload_id","=",$objectload_id)
				->order_by("category")
				->find_all();

		foreach ($files as $file)
		{
			$rec_file = new Obj($file->get_row_as_obj());

			$rec_file->statistic_str 			= $this->get_statistic_row($rec_file->statistic);
			$rec_file->notloaded_records_exists = $this->notloaded_records_exists($rec_file->statistic);
			$rec_file->error_exists 			= $this->error_exists($rec_file->statistic);

			$objectload_files[] = $rec_file;
		}

		return $objectload_files;
	}

	function get_union_subquery_by_category($objectload_id, $category_names = NULL)
	{
		$ol = ORM::factory('Objectload', (is_array($objectload_id)) ? $objectload_id[0] : $objectload_id);

		if (!$ol->loaded())
			return;

		$query = NULL;

		$of = ORM::factory('Objectload_Files')
					->where("objectload_id","IN", (is_array($objectload_id)) ? $objectload_id : array($objectload_id));

		if ($category_names)
			$of = $of->where("category","IN",$category_names);
		
		$of = $of->find_all();

		foreach ($of as $file) {
			if (!$query)
				$query = DB::select('external_id')
							->from("_temp_".$file->table_name);
			else
				$query = DB::select('external_id')
							->from("_temp_".$file->table_name)
							->union($query);
		}

		return $query;

	}

} 
