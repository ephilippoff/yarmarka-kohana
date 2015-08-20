<?php defined('SYSPATH') OR die('No direct script access.');

class Performance {

	protected $token = NULL;
	
	function __construct($enable_profiler = FALSE)
	{

		$this->enable_profiler = $enable_profiler;
		if ($enable_profiler) {

		}
	}

	public static function factory($enable_profiler = FALSE)
	{
		return new Performance($enable_profiler);
	}

	public function add($group, $name)
	{
		if (!$this->enable_profiler) return;
		
		if ($this->token)
		{
			Profiler::stop($this->token);
			$this->token = NULL;
		}

		if ($name <> "end")
			$this->token = Profiler::start($group, $name);
	}

	public function getProfilerStat()
	{
		if (!$this->enable_profiler) return;

		return View::factory('profiler/stats');
	}
}