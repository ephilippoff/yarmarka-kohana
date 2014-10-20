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
		$load->save();

		return $load->id;
	}

	public function saveTempRecordsByLoadedFiles()
	{
		$pl = ORM::factory('Priceload', $this->_priceload_id);
		$pl->table_name = $this->saveRecordsToTempTable();
		$pl->save();
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

				
		
		$f->forEachRow($config, $filepath, function($row, $i) use ($fields, $table_name){
				$t = ORM_Temp::factory($table_name);
				foreach ($fields as $field){
					if ($row->{$field["name"]})
						$t->{$field["name"]} = strip_tags(trim($row->{$field["name"]}));
				}
				$t->save();
		});

		return $table_name;
		
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
			);
	}

	public static function getTypeFields()
	{

		return array(
				"info"    => 'Информационное поле',
				"filter"  =>  'Фильтр',
				"ident"    => 'Уникальный идентификатор',
				"price"    => 'Цена',
				"description" => 'Описание',
			);
	}

}