<?php defined('SYSPATH') or die('No direct script access.');

class Form_Custom_Orginfo extends Form_Custom {

	public $user = NULL;
	public $_settings = NULL;
	public $prefix = NULL;

	function __construct()
	{
		$this->prefix = "orginfo-";
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
						if (!array_key_exists($key, $this->_settings["fields"]))
							continue;

						$field = new Obj($this->_settings["fields"][$key]);
						switch ($field->type) {
							case 'photo':

								if (!array_key_exists("delete_".$key, $data))
									$this->save_photo($key, $value);
								else 
									$this->delete_photo($key);
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

		$size = $this->_settings["fields"][$name]["size"];
		$setting = ORM::factory('User_Settings')
						->where("user_id","=",$this->user)
						->where("name","=",$this->prefix.$name)
						->find();
		$setting->user_id = $this->user->id;
		$setting->name = $this->prefix.$name;
		$setting->value = Uploads::get_file_path($filename, $size);
		$setting->save();

		return FALSE;
	}

	public function delete_photo($name)
	{
		$setting = ORM::factory('User_Settings')
						->where("user_id","=",$this->user)
						->where("name","=",$this->prefix.$name)
						->delete_all();

		return FALSE;
	}
}