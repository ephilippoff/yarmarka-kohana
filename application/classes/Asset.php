<?php defined('SYSPATH') OR die('No direct script access.');

class Asset extends Kohana_Asset {

	function __construct($type, $file, array $options = array())
	{
		parent::__construct($type, $file, $options);
		
		if ( isset($options["only_file_name"]) ) {
			$this->only_file_name = TRUE;
		}
	}

	public function render($process = FALSE)
	{
		if ($this->needs_recompile())
		{
			// Recompile file
			file_put_contents($this->destination_file(), $this->compile($process));
		}

		if ( isset($this->only_file_name) AND $this->only_file_name) {
			return Asset::file_name($this->type(), $this->destination_web(), $this->last_modified());
		} else {
			return Asset::html($this->type(), $this->destination_web(), $this->last_modified());
		}
	}

	public function compile($process = FALSE)
	{
		// Get file contents
		$content = file_get_contents($this->source_file());
		
		return $content;
	}

	static function file_name($type, $file, $last_modified = NULL, $attributes = array())
	{
		if ($last_modified)
		{
			// Add last modified time to file name
			$file = $file.'?'.$last_modified;
		}

		// Set type for the proper HTML
		switch($type)
		{
			case Assets::JAVASCRIPT:
				$type = 'script';
			break;
			case Assets::STYLESHEET:
				$type = 'style';
			break;
		}

		return $file;
	}

}