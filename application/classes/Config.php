<?php defined('SYSPATH') OR die('No direct script access.');

class Config extends Kohana_Config {
	/**
	 * Load a configuration group. Searches all the config sources, merging all the 
	 * directives found into a single config group.  Any changes made to the config 
	 * in this group will be mirrored across all writable sources.  
	 *
	 * FIX: merge only assoc arrays
	 *     $array = $config->load($name);
	 *
	 * See [Kohana_Config_Group] for more info
	 *
	 * @param   string  $group  configuration group name
	 * @return  Kohana_Config_Group
	 * @throws  Kohana_Exception
	 */
	public function load($group)
	{
		if ( ! count($this->_sources))
		{
			throw new Kohana_Exception('No configuration sources attached');
		}

		if (empty($group))
		{
			throw new Kohana_Exception("Need to specify a config group");
		}

		if ( ! is_string($group))
		{
			throw new Kohana_Exception("Config group must be a string");
		}

		if (strpos($group, '.') !== FALSE)
		{
			// Split the config group and path
			list($group, $path) = explode('.', $group, 2);
		}

		if (isset($this->_groups[$group]))
		{
			if (isset($path))
			{
				return Arr::path($this->_groups[$group], $path, NULL, '.');
			}
			return $this->_groups[$group];
		}

		$config = array();

		// We search from the "lowest" source and work our way up
		$sources = array_reverse($this->_sources);

		foreach ($sources as $source)
		{
			if ($source instanceof Kohana_Config_Reader)
			{
				if ($source_config = $source->load($group))
				{
					$config = Arr::merge_assoc($config, $source_config);
				}
			}
		}

		$this->_groups[$group] = new Config_Group($this, $group, $config);

		if (isset($path))
		{
			return Arr::path($config, $path, NULL, '.');
		}

		return $this->_groups[$group];
	}
}