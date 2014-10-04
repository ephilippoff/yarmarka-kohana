<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

	protected $_reload_on_wakeup = FALSE;
	
    /**
     * Updates all existing records
     *
     * @chainable
     * @return  ORM
     */
    public function update_all() 
	{
        $this->_build(Database::UPDATE);

        if (empty($this->_changed)) 
		{
            // Nothing to update
            return $this;
        }

        $data = array();
        foreach ($this->_changed as $column) 
		{
            // Compile changed data
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_updated_column)) 
		{
            // Fill the updated column
            $column = $this->_updated_column['column'];
            $format = $this->_updated_column['format'];

            $data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
        }

        $this->_db_builder->set($data)->execute($this->_db);

        return $this;
    }

    /**
     * Delete all objects in the associated table. This does NOT destroy
     * relationships that have been created with other objects.
     *
     * @chainable
     * @return  ORM
     */
    public function delete_all() 
	{
        $this->_build(Database::DELETE);

        $this->_db_builder->execute($this->_db);

        return $this->clear();
    }

	/**
	 * Fix for COUNT(DISTINCT()) and count by column
	 *
	 * ->distinct(TRUE)->count_all('id')
	 * genereate SELECT COUNT(DISTINCT(id))
	 *
	 * ->count_all('id')
	 * genereate SELECT COUNT('id')
	 * 
	 * @param string $column
	 * @access public
	 * @return integer
	 */
	public function count_all($column = NULL, $cached = FALSE)
	{
		$selects = array();

		foreach ($this->_db_pending as $key => $method)
		{
			if ($method['name'] == 'select')
			{
				// Ignore any selected columns for now
				$selects[] = $method;
				unset($this->_db_pending[$key]);
			}
		}

		if ( ! empty($this->_load_with))
		{
			foreach ($this->_load_with as $alias)
			{
				// Bind relationship
				$this->with($alias);
			}
		}

		$this->_build(Database::SELECT);

		$records = $this->_db_builder->from(array($this->_table_name, $this->_object_name))
			->select(array(DB::expr('COUNT('.( ( $column AND $this->_db_builder->_distinct ) ? 'DISTINCT ' : '' ).( $column ? '"'.$column.'"' : '*' ).')'), 'records_found'));

		if ($cached)
			$records = $records->cached($cached);

		$records = 	$records->execute($this->_db)
			->get('records_found');

		// Add back in selected columns
		$this->_db_pending += $selects;

		$this->reset();

		// Return the total number of records in a table
		return $records;
	}

	public function is_null($column)
	{
		return $this->where($column,"IS", NULL);
	}

	public function is_not_null($column)
	{
		return $this->where($column,"IS NOT", NULL);
	}

	public function more_than_now($column)
	{
		return $this->where($column,">",DB::expr('NOW()'));
	}

	public function more_or_equal_than_now($column)
	{
		return $this->where($column,">=",DB::expr('NOW()'));
	}

	public function temp_find_all()
	{
		if ( ! empty($this->_load_with))
		{
			foreach ($this->_load_with as $alias)
			{
				// Bind auto relationships
				$this->with($alias);
			}
		}

		$this->_build(Database::SELECT);
		$this->_db_builder->from(array($this->_table_name, $this->_object_name));
		$this->_db_builder->select_array($this->_build_select());

		if ( ! isset($this->_db_applied['order_by']) AND ! empty($this->_sorting))
		{
			foreach ($this->_sorting as $column => $direction)
			{
				if (strpos($column, '.') === FALSE)
				{
					// Sorting column for use in JOINs
					$column = $this->_object_name.'.'.$column;
				}

				$this->_db_builder->order_by($column, $direction);
			}
		}
		$result = $this->_db_builder->from(array($this->_table_name, $this->_object_name))->as_object('Obj')->execute($this->_db);

		$this->reset();

		return $result;
	}

	public function get_row_as_obj()
	{
		$o = unserialize($this->serialize());
		return new Obj($o["_object"]);
	}
}
