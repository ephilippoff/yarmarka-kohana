<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

	protected $_reload_on_wakeup = FALSE;

	protected $_time_link_cache = 0;
	
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
	public function count_all($column = NULL, $cached = FALSE, $tag = NULL)
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

	/* переопределение метода get  с кешированием связей*/
	public function get($column)
	{
		if (array_key_exists($column, $this->_object))
		{
			return (in_array($column, $this->_serialize_columns))
				? $this->_unserialize_value($this->_object[$column])
				: $this->_object[$column];
		}
		elseif (isset($this->_related[$column]))
		{
			// Return related model that has already been fetched
			return $this->_related[$column];
		}
		elseif (isset($this->_belongs_to[$column]))
		{
			$model = $this->_related($column);

			// Use this model's column and foreign model's primary key
			$col = $model->_object_name.'.'.$model->_primary_key;
			$val = $this->_object[$this->_belongs_to[$column]['foreign_key']];

			// Make sure we don't run WHERE "AUTO_INCREMENT column" = NULL queries. This would
			// return the last inserted record instead of an empty result.
			// See: http://mysql.localhost.net.ar/doc/refman/5.1/en/server-session-variables.html#sysvar_sql_auto_is_null
			if ($val !== NULL)
			{
				$model->where($col, '=', $val)->cached($this->_time_link_cache)->find();
			}

			return $this->_related[$column] = $model;
		}
		elseif (isset($this->_has_one[$column]))
		{
			$model = $this->_related($column);

			// Use this model's primary key value and foreign model's column
			$col = $model->_object_name.'.'.$this->_has_one[$column]['foreign_key'];
			$val = $this->pk();

			$model->where($col, '=', $val)->cached($this->_time_link_cache)->find();

			return $this->_related[$column] = $model;
		}
		elseif (isset($this->_has_many[$column]))
		{
			$model = ORM::factory($this->_has_many[$column]['model']);

			if (isset($this->_has_many[$column]['through']))
			{
				// Grab has_many "through" relationship table
				$through = $this->_has_many[$column]['through'];

				// Join on through model's target foreign key (far_key) and target model's primary key
				$join_col1 = $through.'.'.$this->_has_many[$column]['far_key'];
				$join_col2 = $model->_object_name.'.'.$model->_primary_key;

				$model->join($through)->on($join_col1, '=', $join_col2);

				// Through table's source foreign key (foreign_key) should be this model's primary key
				$col = $through.'.'.$this->_has_many[$column]['foreign_key'];
				$val = $this->pk();
			}
			else
			{
				// Simple has_many relationship, search where target model's foreign key is this model's primary key
				$col = $model->_object_name.'.'.$this->_has_many[$column]['foreign_key'];
				$val = $this->pk();
			}

			return $model->where($col, '=', $val);
		}
		else
		{
			throw new Kohana_Exception('The :property property does not exist in the :class class',
				array(':property' => $column, ':class' => get_class($this)));
		}
	}

	/*
		устанавливает время кеширования для запросов сделанных через орм belongs_to и has_one
	*/
	public function set_time_link_cache($time)
	{
		if ((int) $time)
			$this->_time_link_cache = (int) $time;

		return $this;
	}

	public function cached($lifetime = NULL, $tag = NULL, $force = FALSE)
	{
		// Add pending database call which is executed after query type is determined
		$this->_db_pending[] = array(
			'name' => 'cached',
			'args' => array($lifetime, $force, $tag),
		);

		return $this;
	}

	public function where_cached($left, $contintion, $right, $cached = 0)
	{
		if ($cached == 0)
				return $this->where($left,$contintion,$right)
					->cached($cached, NULL, TRUE);
		else
			return $this->where($left,$contintion,$right)
					->cached($cached);
	}

	public function getprepared_all()
	{
		$result = array();
		foreach ($this->find_all() as $item) {
			array_push($result, (array) $item->get_row_as_obj());
		}

		return $result;
	}

}
