<?php defined('SYSPATH') or die('No direct script access.');

class Database_Query extends Kohana_Database_Query {
	/**
	 * FIX: use global defined cache instead of file cache
	 *
	 * @param   mixed    Database instance or name of instance
	 * @param   string   result object classname, TRUE for stdClass or FALSE for array
	 * @param   array    result object constructor arguments
	 *
	 * @return  object   Database_Result for SELECT queries
	 * @return  mixed    the insert id for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	public function execute($db = NULL, $as_object = NULL, $object_params = NULL)
	{
		if ( ! is_object($db))
		{
			// Get the database instance
			$db = Database::instance($db);
		}

		if ($as_object === NULL)
		{
			$as_object = $this->_as_object;
		}

		if ($object_params === NULL)
		{
			$object_params = $this->_object_params;
		}

		// Compile the SQL query
		$sql = $this->compile($db);

		if ($this->_lifetime !== NULL AND $this->_type === Database::SELECT)
		{
			// Set the cache key based on the database instance name and SQL
			$cache_key = sha1('Database::query("'.$db.'", "'.$sql.'")');

			// Read the cache first to delete a possible hit with lifetime <= 0
			if (($result = Cache::instance('memcache')->get($cache_key)) !== NULL
				AND ! $this->_force_execute)
			{
				// Return a cached result
				return new Database_Result_Cached($result, $sql, $as_object, $object_params);
			}
		}

		// Execute the query
		$result = $db->query($this->_type, $sql, $as_object, $object_params);

		if (isset($cache_key) AND $this->_lifetime > 0)
		{
			// Cache the result array
			Cache::instance('memcache')->set($cache_key, $result->as_array(), $this->_lifetime);
		}

		return $result;
	}

} // End Database_Query
