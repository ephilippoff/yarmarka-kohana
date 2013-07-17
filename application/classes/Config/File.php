<?php defined('SYSPATH') OR die('No direct script access.');

class Config_File extends Kohana_Config_File {
	/**
	 * Load and merge all of the configuration files in this group. 
	 * FIX: Merge only associate arrays
	 *
	 *     $config->load($name);
	 *
	 * @param   string  $group  configuration group name
	 * @return  $this   current object
	 * @uses    Kohana::load
	 */
	public function load($group)
	{
		$config = array();

		if ($files = Kohana::find_file($this->_directory, $group, NULL, TRUE))
		{
			foreach ($files as $file)
			{
				// Merge each file to the configuration array
				$config = Arr::merge_assoc($config, Kohana::load($file));
			}
		}

		return $config;
	}

}