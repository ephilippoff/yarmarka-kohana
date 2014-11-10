<?php defined('SYSPATH') or die('No direct script access.');

class Form_Custom_Orginfo extends Form_Custom {

	public $user = NULL;
	private $prefix = "orginfo-";

	function __construct()
	{
		$this->_settings = Kohana::$config->load("form/custom.orginfo");
		$this->user = Auth::instance()->get_user();
	}

	public function save(array $data)
	{
		if (parent::save($data))
		{
			$db = Database::instance();
			try
			{
				$db->begin();
				foreach ($data as $key => $value) {
					if ($value)
					{
						$field = new Obj($this->_settings["fields"][$key]);
						switch ($field->type) {
							case 'photo':
								$this->save_photo($key, $value);
							break;
							
							default:
								$this->save_param($key, $value);
							break;
						}
					}
				}
				$db->commit();
			}
			catch(Exception $e)
			{
				$db->rollback();
			}
		}
	}

	public function get_data()
	{
		$data = array();
		$settings = ORM::factory('User_Settings')
					->where("user_id","=",$this->user)
					->where("name","LIKE",$this->prefix."%")
					->find_all();

		foreach ($settings as $setting) {
			@list($type, $name) = explode("-",$setting->name);
			$data[$name] = $setting->value;
		}

		return $data;
	}

	public function save_param($name, $value)
	{
		if (!$value)
			return;

		$setting = ORM::factory('User_Settings')
						->where("user_id","=",$this->user)
						->where("name","=",$this->prefix.$name)
						->find();
		$setting->user_id = $this->user->id;
		$setting->name = $this->prefix.$name;
		$setting->value = $value;
		$setting->save();

		return $setting;
	}

	public function save_photo($name, $value)
	{
		$file = $_FILES[$name];
		try {
			$filename = Uploads::save($file);
		} catch (Exception $e)
		{
			return;
		}
		$setting = ORM::factory('User_Settings')
						->where("user_id","=",$this->user)
						->where("name","=",$this->prefix.$name)
						->find();
		$setting->user_id = $this->user->id;
		$setting->name = $this->prefix.$name;
		$setting->value = Uploads::get_file_path($filename, '272x203');
		$setting->save();

		return FALSE;
	}
}