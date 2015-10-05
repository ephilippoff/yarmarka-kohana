<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Kupon_Group extends ORM {

	protected $_table_name = 'kupon_group';

	function get_by_object($object_id)
	{
		return $this->where("object_id","=", $object_id)->find_all();
	}

	function get_kupon($quantity = 1)
	{
		$kupons = ORM::factory('Kupon')->get_avail($this->id)->find_all();
		$result = array();
		foreach ($kupons as $kupon) {
			$result[] = $kupon->id;
			if (count($result) >= $quantity) break;
		}

		return $result;
	}

	function get_balance()
	{
		return ORM::factory('Kupon')->get_avail_count($this->id);
	}

} 
