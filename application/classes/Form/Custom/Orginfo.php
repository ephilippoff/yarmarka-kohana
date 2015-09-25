<?php defined('SYSPATH') or die('No direct script access.');

class Form_Custom_Orginfo extends Form_Custom {

	public $user = NULL;
	public $_settings = NULL;
	public $prefix = NULL;

	function __construct()
	{
		$this->prefix = "orginfo";
		$this->_settings = Kohana::$config->load("form/custom.orginfo");
		$this->user = Auth::instance()->get_user();
	}

	public function save(array $data)
	{

		if (!parent::save($data))
			return FALSE;

		foreach ($this->_settings["fields"] as $fieldname => $settings) {
			if (isset($data[$fieldname]))
				$this->save_param($fieldname, $data[$fieldname]);
		}
		return TRUE;
	}

	public function get_data()
	{
		return ORM::factory('User_Settings')
					->get_group($this->user, $this->prefix);
	}

	public function save_param($name, $value)
	{	
		if (!$value)
		{
			return ORM::factory('User_Settings')
					->_delete($this->user, $this->prefix, $name);
		}
		return ORM::factory('User_Settings')
						->update_or_save($this->user, $this->prefix, $name, $value);
	}
}