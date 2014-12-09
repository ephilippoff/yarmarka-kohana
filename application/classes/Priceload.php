<?php defined('SYSPATH') or die('No direct script access.');

class Priceload 
{
	public $_priceload_id;
	public $_user_id;
	public $_filepath;
	public $_settings = array(

		);

	function __construct($user_id, Obj $settings, $priceload_id = NULL)
	{
		$this->_user_id 	  = $user_id;
		if (!$priceload_id)
			$this->_priceload_id = $this->initRecord($user_id, $settings);
		elseif ($priceload_id == 'last')
			$this->_priceload_id = ORM::factory('Priceload')->order_by("id","desc")->find()->id;
		else
			$this->_priceload_id = $priceload_id;
	}

	private function initRecord($user_id, $settings)
	{
		if (!$settings->file)
			return FALSE;



		$f = new Massload_File();
		@list($filepath, $imagepath) = $f->init($settings->file, $user_id);

		$load = ORM::factory('Priceload');
		$load->user_id  = $user_id;
		$load->title    = $settings->title;
		$load->filepath = $this->_filepath = $filepath;
		$load->filepath_original =  $filepath;
		$load->save();

		return $load->id;
	}

	public function saveTempRecordsByLoadedFiles($edit = FALSE)
	{
		$pl = ORM::factory('Priceload', $this->_priceload_id);
		if (!$edit)
		{
			$pl->table_name = $this->saveRecordsToTempTable();
			$pl->save();
		} else {
			$f = new Massload_File();
			$config = unserialize($pl->config);

			$config["fields"] = array();
			foreach (explode(",", $config["columns"]) as $column) {
				if (isset($config[$column."_title"]))
				$config["fields"][$column] = array("name" => $column, "type" => $config[$column."_type"]);
			}

			$fields 	= array_merge($config["fields"], Priceload::getServiceFields());
			$this->saveRowsToTempTable($f, $config, $this->_filepath, $fields, $pl->table_name);
		}
	}

	public function saveRecordsToTempTable()
	{

		$filepath 		= $this->_filepath;

		$table_name = Temptable::get_name(array("price", $this->_user_id));

		$f = new Massload_File();

		$fields = array();
		$config = array("fields" => array());
		$options = new Obj();

		$f->forEachField($filepath, $options, function($num, $fieldname) use (&$config, $table_name){
			$config["fields"]["f".$num] = array("name" => "f".$num, "type" => "text");
		});
		
		$fields 	= array_merge($config["fields"], Priceload::getServiceFields());
		Temptable::create_table($table_name, $fields);

		$this->saveRowsToTempTable($f, $config, $filepath, $fields, $table_name);

		return $table_name;
		
	}

	public function saveRowsToTempTable(Massload_File $f, $config, $filepath, $fields, $table_name)
	{
		$f->forEachRow($config, $filepath, function($row, $i) use ($fields, $table_name){
				$t = ORM_Temp::factory($table_name);
				foreach ($fields as $field){
					if ($row->{$field["name"]})
						$t->{$field["name"]} = strip_tags(trim($row->{$field["name"]}));
				}
				$t->save();
		});
	}

	public static function getFieldsFromConfig($config, $typefield = NULL)
	{
		if (!$config)
			return;

		$result = array();
		$fields = explode(",", $config->columns);
		foreach ($fields as $field) {
			$title = $config->{$field.'_title'};
			$type = $config->{$field.'_type'};
			if (($type AND !$typefield) OR ($typefield AND $typefield == $type))
				$result[$field] = array("title"=>$title,"type"=>$type);
		}
		return $result;
	}

	public static function getFiltersHierarchy($queryResult)
	{
		$tree = array();
		foreach ($queryResult as $_row) {
			$row_keys = array_reverse(array_keys($_row));
			$row = array_reverse(array_values($_row));

			$level = count($row)-1;

			while ($level >= 0) {
				if ($level == count($row)-1)
					$temp = &$tree[ $row_keys[$level]."_".$row[$level] ];
				else
					$temp = &$temp[ $row_keys[$level]."_".$row[$level] ];
				$level--;
			}

		}
		return $tree;
	}

	public static function createSimpleFilters($priceload_id)
	{
		$priceload = ORM::factory('Priceload',$priceload_id);
		$table_name = $priceload->table_name;

		$config = new Obj(unserialize($priceload->config));

		$fields = Priceload::getFieldsFromConfig($config, "filter");

		ORM::factory('Priceload_Attribute')
				->where("priceload_id","=",$priceload_id)
				->where("type","=", "simple")
				->delete_all();

		$filters = array();
		foreach ($fields as $field_key => $field_value) {			

			$pa = ORM::factory('Priceload_Attribute');
			$pa->priceload_id = $priceload_id;
			$pa->title = $field_value["title"];
			$pa->column = $field_key;
			$pa->type = "simple";
			$pa->save();

			ORM::factory('Priceload_Filter')
				->where("priceload_id","=",$priceload_id)
				->where("priceload_attribute_id","=",$pa->id)
				->delete_all();

			$_filters = DB::select($field_key, DB::expr("count(".$field_key.")"))->from("_temp_".$table_name)
		 				->group_by($field_key)
		 				->order_by($field_key,"asc")
		 				->execute()->as_array($field_key);

		 	foreach ($_filters as  $filter) {
		 		$filter["column"] = $field_key;
		 		$filter["title"] = $filter[$field_key];
		 		$filter["priceload_attribute"] = $pa->id;
		 		$filters[] = $filter;
		 	}
		}

		

		foreach ($filters as $filter) {
			if ($filter["title"] == "" OR !$filter["title"])
				continue;

			$_filtered_rows = DB::select("id")->from("_temp_".$priceload->table_name)
							->where($filter["column"],"=",$filter["title"])							
			 				->execute()->as_array("id");
			$filtered_rows = serialize(array_keys($_filtered_rows));

			$pf = ORM::factory('Priceload_Filter');
			$pf->title = $filter["title"];
			$pf->column = $filter["column"];
			$pf->priceload_id = $priceload_id;
			$pf->count = $filter["count"];
			$pf->filtered_rows = $filtered_rows;
			$pf->priceload_attribute_id = $filter["priceload_attribute"];
			$pf->save();
		}	
		self::resetObjectCache($priceload_id);
	}

	public static function createHierarchyFilters($priceload_id)
	{
		$priceload = ORM::factory('Priceload',$priceload_id);
		$table_name = $priceload->table_name;

		$config = new Obj(unserialize($priceload->config));

		$fields = Priceload::getFieldsFromConfig($config, "hierarchy_filter");

		if (!count($fields))
			return;

		// создаем иерархический фильтр
		$query = DB::select(DB::expr(implode(",",array_keys($fields))))
						->from("_temp_".$priceload->table_name)
		 				->execute()->as_array();

		$tree = Priceload::getFiltersHierarchy($query);

		ORM::factory('Priceload_Attribute')
			->where("priceload_id","=",$priceload_id)
			->where("type","=", "hierarchy")
			->delete_all();

		$pa = ORM::factory('Priceload_Attribute');
		$pa->priceload_id = $priceload_id;
		$pa->title = "hierarchy";
		$pa->type = "hierarchy";
		$pa->save();

		$hierarchy_id = $pa->id;

		ORM::factory('Priceload_Filter')
				->where("priceload_id","=",$priceload_id)
				->where("priceload_attribute_id","=",$hierarchy_id)
				->delete_all();

		self::recurseHierarchyFilters($tree, function($key, $level, $parents, $parent_id) 
													use ($table_name, $priceload_id, $hierarchy_id){

			@list($column, $title) = explode("_", $key);

			$_filtered_rows = DB::select("id")->from("_temp_".$table_name);
			foreach ($parents as $pkey => $pvalue) {
				$_filtered_rows = $_filtered_rows->where($pkey,"=",$pvalue);
			}

			

			$_filtered_rows = $_filtered_rows->execute()->as_array("id");

			if (!count($_filtered_rows))
				continue;

			$filtered_rows = serialize(array_keys($_filtered_rows));

			$pf = ORM::factory('Priceload_Filter');
			$pf->title 					= $title;
			$pf->column 				= $column;
			$pf->priceload_id 			= $priceload_id;
			$pf->count 					= count($_filtered_rows);
			$pf->filtered_rows 			= $filtered_rows;
			$pf->priceload_attribute_id = $hierarchy_id;
			$pf->parent_id = $parent_id;
			$pf->save();

			return $pf->id;
		});

		self::resetObjectCache($priceload_id);
	}

	public static function resetObjectCache($priceload_id)
	{
		$op =ORM::factory('Object_Priceload')
				->where("priceload_id","=",$priceload_id)
				->find_all();
		foreach ($op as $price) {
			Cache::instance('memcache')->delete("landing:{$price->object_id}");
		}
		
	}

	public static function recurseHierarchyFilters($data, $callback, $level = 0, $parents = array(), $parent_id = NULL)
	{
		foreach ($data as $key => $value) {
			@list($f, $v) = explode("_", $key);
			$parents[$f] = $v;
			$filter_id = $callback($key, $level, $parents, $parent_id);
			if (is_array($value))
			{
				$level++;
				self::recurseHierarchyFilters($value, $callback, $level, $parents, $filter_id);
			}
			
		}
	}

	public function setState($state = 0, $comment = NULL)
	{
		$pl = ORM::factory('Priceload', $this->_priceload_id)
					->set_state($state, $comment);
		return $state;
	}

	public static function getServiceFields()
	{

		return array(
				"price"    => array(
					"name" => "price",
					"type" => "int",
				),
				"description"    => array(
					"name" => "description",
					"type" => "text",
				),
				"error"    => array(
					"name" => "error",
					"type" => "int",
				),
				"text_error" => array(
					"name" => "text_error",
					"type" => "text",
				),
				"image" => array(
					"name" => "image",
					"type" => "path",
				),
			);
	}

	public static function getTypeFields()
	{

		return array(
				"no"    => 'Отключено',
				"info"    => 'Информационное поле',
				"filter"  =>  'Фильтр',
				"hierarchy_filter"  =>  'Иерархический фильтр',
				"ident"    => 'Уникальный идентификатор',
				"price"    => 'Цена',
				"description" => 'Описание'
			);
	}

}