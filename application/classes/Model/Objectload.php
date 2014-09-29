<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Objectload extends ORM {

	protected $_table_name = 'objectload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

	/*
		state may be:

		0 - default
		1 - on_moderation (active)
		2 - true_moderation (active)
		3 - false_moderation (active, end state) 
		4 - in order/in proccess (active)
		5 - finished (end state)
		99 - error (active)
	*/
	function set_state($state = 0, $comment = NULL)
	{
		if (!$this->loaded())
			return;

		$this->state = $state;
		$this->comment = $comment;
		$this->update();
	}

	function get_states()
	{
		return array(
    			0 => "у оператора",
    			1 => "на модерации",
    			2 => "одобрено",
    			3 => "отклонено",
    			4 => "в обработке",
    			5 => "выполнено",
    			99 => "ошибка"
    		);
	}

	function get_active_states()
	{
		return array(1,2,3,4,99);
	}

	function get_foruserdelete_states()
	{
		return array(1,2,3,99);
	}

	function get_refresh_states()
	{
		return array(1,2,99);
	}

	function get_withcomment_states()
	{
		return array(3,99);
	}

	function get_statistic()
	{
		if (!$this->loaded())
			return;

		return unserialize($this->statistic);
	}

	function get_statistic_row($item)
	{
		if (!$item)
			return 

		$statstr = '';
		$errorstr ='';
		if ($item->statistic) {
			$statistic = new Obj(unserialize($item->statistic));
			$new = $statistic->loaded - $statistic->edited;
			if($statistic->error>0)
			{
				$percent = 0;
				if ($statistic->all<>0)
					$percent = round(($statistic->error/$statistic->all)*100);

				$allow_percent = Kohana::$config->load('massload.allow_error_percent');
				
				$color = "red";
				if ($percent < $allow_percent)
					$color = "green";

				$errorstr = "<span style='color:$color;'>".$statistic->error." (".$percent."%)</span>";
			}
			$statstr = $new." / ".$statistic->edited." / ".$statistic->nochange." / ".$errorstr." = ".$statistic->all;
	
		} else {
			$statstr = '';
		}
		return $statstr;
	}

	function update_statistic()
	{
		if (!$this->loaded())
			return;

		$common_statistic = array(
				"all" => 0,
				"loaded" => 0,
				"error"  => 0,
				"edited" => 0,
				"nochange" => 0
			);

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id","=", $this->id)
				->where("table_name","IS NOT", NULL)
				->find_all();
		foreach ($of as $file)
		{
			$statistic = array();
			$common_statistic["all"]    += $statistic["all"]   = ORM_Temp::factory($file->table_name)
																	->count_all();

			$common_statistic["loaded"] += $statistic["loaded"] = ORM_Temp::factory($file->table_name)
																	->where("loaded","=",1)
																	->count_all();

			$common_statistic["error"] += $statistic["error"]  = ORM_Temp::factory($file->table_name)
																	->where("error","=",1)
																	->count_all();

			$common_statistic["edited"] += $statistic["edited"] = ORM_Temp::factory($file->table_name)
																	->where("edited","=",1)
																	->count_all();

			$common_statistic["nochange"] += $statistic["nochange"] = ORM_Temp::factory($file->table_name)
																	->where("nochange","=",1)
																	->count_all();

			ORM::factory('Objectload_Files')
				->where("id","=",$file->id)
				->set("statistic",serialize($statistic))
				->update_all();
		}

		$this->statistic = serialize($common_statistic);
		$this->update();

		return $this;
	}

	function _delete()
	{
		if (!$this->loaded())
			return FALSE;

		$of = ORM::factory('Objectload_Files')
				->where("objectload_id", "=", $this->id)
				->where("table_name","IS NOT",NULL)
				->find_all();
		foreach($of as $file)
		{
			try {
				Temptable::delete_table($file->table_name);
				$of_update = ORM::factory('Objectload_Files', $file->id);
				$of_update->table_name = NULL;
				$of_update->update();
			} catch (Exception $e)
			{

			}
		}

		$this->delete();
	}

	function get_objectload_list($orm_objectloads)
	{
		if (!$orm_objectloads)
			return;

		$objectload_files   = ORM::factory('Objectload_Files');
		$objectloads = array();


		foreach ($orm_objectloads as $load)
		{
			$rec_load = $load->get_row_as_obj();
			
			$rec_load->objfiles 		  = $objectload_files->get_objectload_files_list($load->id);
			$rec_load->email 			  = $load->user->email;
			$rec_load->access_userdelete  = in_array( $rec_load->state, $this->get_foruserdelete_states() );
			$rec_load->access_refresh     = in_array( $rec_load->state, $this->get_refresh_states() );
			$rec_load->is_active     	  = in_array( $rec_load->state, $this->get_active_states() );
			$rec_load->withcomment_state  = in_array( $rec_load->state, $this->get_withcomment_states() );
			$rec_load->statistic_str	  = $this->get_statistic_row($rec_load);

			$objectloads[] = $rec_load;
		}

		return $objectloads;

	}

	public function get_categories_flatarray($objectload_id)
	{
		$of = DB::select("category")
					->from('objectload_files')
					->where("objectload_id","=",$objectload_id)
					->group_by("category")
					->as_object()
					->execute();

		$category_ids = array();
		foreach ($of as $file) {
			
			$config = Kohana::$config->load('massload/bycategory.'.$file->category);

			if (array_key_exists($config["id"], $category_ids))
				array_push($category_ids[$config["id"]], $config["category"]);
			else
				$category_ids[$config["id"]] = array($config["category"]);

		}

		return $category_ids;
	}

	function unpublish_expired($callback)
	{
		if (!$this->loaded())
			return;
		
		$flatcategories =  $this->get_categories_flatarray($this->id);

		if ($flatcategories)
			foreach ($flatcategories as $category_id => $category_names) {
				$callback("Start", join(",", $category_names));
				$count = ORM::factory('Object')
						->unpublish_expired_in_objectload_category($this->id, $this->user_id, $category_id, $category_names);
				$callback("End. ".$count." adverts affected",  join(",", $category_names));
			}
	}

	function publish_and_prolonge($callback)
	{
		if (!$this->loaded())
			return;

		$flatcategories =  $this->get_categories_flatarray($this->id);

		if ($flatcategories)
			foreach ($flatcategories as $category_id => $category_names) {
				$callback("Start", join(",", $category_names));
				$count = ORM::factory('Object')
						->publish_and_prolonge_objectload($this->id, $this->user_id);
				$callback("End. ".$count." adverts affected",  join(",", $category_names));
			}

	}

} 
