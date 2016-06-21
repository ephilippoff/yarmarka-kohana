<?php defined('SYSPATH') OR die('No direct script access.');

class Database_PDO extends Kohana_Database_PDO {
	public function list_columns($table, $like = NULL, $add_prefix = TRUE)
	{
		// Quote the table name
		$table = ($add_prefix === TRUE) ? $table : $table;

		$key = md5($table.($like ? $like : "").($add_prefix ? "TRUE": ""));

		if ( $result = Cache::instance('memcache')->get("Database_PDO_list_columns:$key")) {
			return unserialize($result);
		}

		

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

		Cache::instance('memcache')->set("Database_PDO_list_columns:$key", serialize($columns), Date::WEEK);
		
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

	public function insert_id()
	{
		$query = DB::query(Database::SELECT, 'SELECT LASTVAL() AS insert_id', FALSE)->execute();
		$insert_id = $query->get('insert_id');

		return $insert_id;
	}

	// FIX last_insert_id
	public function query($type, $sql, $as_object = FALSE, array $params = NULL)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if (Kohana::$profiling)
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		try
		{
			$result = $this->_connection->query($sql);
		}
		catch (Exception $e)
		{
			if (isset($benchmark))
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			// Convert the exception in a database exception
			throw new Database_Exception(':error [ :query ]',
				array(
					':error' => $e->getMessage(),
					':query' => $sql
				),
				$e->getCode());
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT)
		{
			// Convert the result into an array, as PDOStatement::rowCount is not reliable
			if ($as_object === FALSE)
			{
				$result->setFetchMode(PDO::FETCH_ASSOC);
			}
			elseif (is_string($as_object))
			{
				$result->setFetchMode(PDO::FETCH_CLASS, $as_object, $params);
			}
			else
			{
				$result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
			}

			$result = $result->fetchAll();

			// Return an iterator of results
			return new Database_Result_Cached($result, $sql, $as_object, $params);
		}
		elseif ($type === Database::INSERT)
		{
			// Return a list of insert id and rows created
			return array(
				$this->insert_id(),
				$result->rowCount(),
			);
		}
		else
		{
			// Return the number of rows affected
			return $result->rowCount();
		}
	}
}
