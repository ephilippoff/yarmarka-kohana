<?php defined('SYSPATH') or die('No direct script access.');

class StaticFile {

	protected $name = NULL;
	protected $filename = NULL;
	protected $script = NULL;
	protected $path = NULL;

	public $jspath = NULL;

	static public function getStaticPath()
	{
		return "../www".Config::getStaticPath()."js/";
	}

	public function __construct($name, $filename)
	{
		$this->name = $name;
		$this->filename = $filename;
		$this->path = APPPATH.self::getStaticPath().$filename;
		$this->jspath = $filename;

		$user = Auth::instance()->get_user();
		if ($user){
			$set = ORM::factory('User_Settings')->get_by_name($user->id, "clearcache")->find();
			if ($set->loaded()){
				var_dump($this);die;
				call_user_func(array($this, $this->name));
				return;
			}
		
		} 

		if (!file_exists($this->path))
			call_user_func(array($this, $this->name));
	}

	public function attributes()
	{
		$data = Attribute::getData();
		$this->script = "var data = eval(".json_encode($data).");";
		$this->save();
	}

	public function save()
	{
		if ($this->script AND $this->path)
		{
			$fp = fopen($this->path, "w");
			fwrite($fp, $this->script);
			fclose($fp);

			if ($user = Auth::instance()->get_user()){
				$us = ORM::factory('User_Settings')
							->get_by_name($user->id, "clearcache")
							->find();
				if ($us->loaded())
					$us->delete();
			}
		}
	}
}

/*
Cache::instance()->get('staticdatafile_'.$this->name);
Cache::instance()->set('staticdatafile_'.$this->name, TRUE);
*/