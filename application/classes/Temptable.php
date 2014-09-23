<?php defined('SYSPATH') or die('No direct script access.');

class Temptable
{
	public static function get_name(array $name_parts)
	{
		
		return implode("_", $name_parts)."_".date('Ymd_His');
	}

	public static function create_table($table_name, $fields)
	{
		if (!count($fields) OR !$table_name)
			return FALSE;
		
		$sql = "CREATE TABLE _temp_$table_name (";
		$f = array();
		$f[] = 'id serial';
		foreach ($fields as $field) {
			$fname = str_replace ( "-", "___", $field["name"]);
			$f[] = $fname." ".Temptable::to_postgres_type($field["type"]);
		}
		$sql .= implode(",", $f);
		$sql .= ", CONSTRAINT ".$table_name."_pkey PRIMARY KEY (id)";
		$sql .= ");";

		return DB::query(NULL, $sql)
			->execute();

	}

	public static function delete_table($table_name)
	{
		if (!$table_name)
			return FALSE;

		$sql = "DROP TABLE _temp_$table_name";
		return DB::query(NULL, $sql)
			->execute();
	}

	private static function to_postgres_type($type)
	{
		switch ($type) {
			case 'text':
			case 'textadv':
			case 'photo':
				return 'text';
			break;
			case 'int':
				return 'integer';
			break;
			default:
				return 'character varying(250)';
			break;
		}
	}
	
}