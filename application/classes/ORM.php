<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

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
}
