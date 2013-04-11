<?php defined('SYSPATH') OR die('No direct script access.');

class Database_PDO extends Kohana_Database_PDO {
	public function list_columns($table, $like = NULL, $add_prefix = TRUE)
	{
		// Quote the table name
		$table = ($add_prefix === TRUE) ? $table : $table;

		if (is_string($like))
		{
			// Search for column names
			$result = $this->query(Database::SELECT, "SELECT * FROM information_schema.columns WHERE table_name LIKE {$like}
					ORDER BY ordinal_position", FALSE);
		}
		else
		{
			// Find all column names
			$result = $this->query(Database::SELECT, "SELECT * FROM information_schema.columns WHERE table_name = '{$table}' 
					ORDER BY ordinal_position", FALSE);
		}

		$columns = array();
		foreach ($result as $column)
		{
			$type	= $column['data_type'];
			$column = array_merge($column, $this->datatype($type));

			$column['data_type']        = $type;
			$column['is_nullable']      = (isset($column['is_nullable']) AND $column['is_nullable'] == 'YES');

			$columns[$column['column_name']] = $column;
		}

		return $columns;
	}

	public function list_tables($like = NULL)
	{
		$this->_connection or $this->connect();

		$sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = '.$this->quote($this->schema());

		if (is_string($like))
		{
			$sql .= ' AND table_name LIKE '.$this->quote($like);
		}

		return $this->query(Database::SELECT, $sql, FALSE)->as_array(NULL, 'table_name');
	}
}
