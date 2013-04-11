<?php defined('SYSPATH') OR die('No direct script access.');

class Database_PDO extends Kohana_Database_PDO {
	public function list_columns($table, $like = NULL, $add_prefix = TRUE)
	{
		// Quote the table name
		$table = ($add_prefix === TRUE) ? $table : $table;

		if (is_string($like))
		{
			// Search for column names
			$result = $this->query(Database::SELECT, 'SELECT * FROM information_schema.columns WHERE table_name LIKE '.$like, FALSE);
		}
		else
		{
			// Find all column names
			$result = $this->query(Database::SELECT, "SELECT * FROM information_schema.columns WHERE table_name = '{$table}'", FALSE);
		}

		$count = 0;
		$columns = array();
		foreach ($result as $column)
		{
			$type	= $column['data_type'];
			$column = array_merge($column, $this->datatype($type));

			$column['data_type']        = $type;
			$column['is_nullable']      = (isset($column['is_nullable']) AND $column['is_nullable'] == 'YES');

			$columns[$column['column_name']] = $column;
		}
		$columns = array_reverse($columns);

		return $columns;
	}
}
