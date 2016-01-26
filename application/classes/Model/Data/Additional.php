<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Additional extends Data
{
	protected $_table_name = 'data_additional';

	public function set_additional($id, $params)
	{
		$additional = $this->where("object","=",$id)
							->find();

		$additional->object = $id;
		$additional->value = serialize($params);
		$additional->save();
	}

	public function get_additional($id)
	{
		$additional = $this->where("object","=",$id)
							->find();
		if ($additional->loaded()) {
			return ($additional->value) ? unserialize($additional->value) : array();
		}
		return FALSE;
	}
}