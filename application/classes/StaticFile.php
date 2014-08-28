<?php defined('SYSPATH') or die('No direct script access.');

class StaticFile {

	protected $name = NULL;
	protected $filename = NULL;
	protected $script = NULL;
	protected $path = NULL;

	public $jspath = NULL;

	const PATH = "../www/uploads/";

	public function __construct($name, $filename)
	{
		$this->name = $name;
		$this->filename = $filename;
		$this->path = APPPATH.self::PATH.$filename;
		$this->jspath = "../../uploads/".$filename;

		$user = Auth::instance()->get_user();
		if ($user){
			$set = ORM::factory('User_Settings')-> get_by_name($user->id, "nocache")->find();
			if ($set->loaded()){
				call_user_func(array($this, $this->name));
				return;
			}
		
		} 

		if (!file_exists($this->path))
			call_user_func(array($this, $this->name));
		else {
			if (!Cache::instance()->get('staticdatafile_'.$this->name)){
				call_user_func(array($this, $this->name));
			}
		}
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
			Cache::instance()->set('staticdatafile_'.$this->name, TRUE);
		}
	}
}