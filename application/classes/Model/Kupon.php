<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Kupon extends ORM
{
	protected $_table_name = 'kupon';

	const INITIAL		= "initial";
	const AVAIL		= "avail";
	const RESERVE		= "reserve";
	const SOLD		= "sold";

	protected $_states = array(
		"initial" => "исх", "avail" => "доступен", "reserve" => "в резерве", "sold" => "продан"
	);

	function get_balance_by_begin_state($kupon_id, $state = "avail", $order_id = NULL)
	{
		$query = DB::select(array(DB::expr('SUM("count")'), 'begin_count'))
							->from('object_movement')
								->where("kupon_id","=",$kupon_id)
								->where("begin_state","=", $state);
		if ($order_id) {
			$query = $query->where("order_id", "=", $order_id);
		}

		return $query;
	}

	function get_balance_by_end_state($kupon_id, $state = "avail", $order_id = NULL)
	{
		$query = DB::select(array(DB::expr('SUM("count")'), 'end_count'))
							->from('object_movement')
								->where("kupon_id","=",$kupon_id)
								->where("end_state","=", $state);
		if ($order_id) {
			$query = $query->where("order_id", "=", $order_id);
		}

		return $query;
	}

	function get_balance($state = "avail", $order_id = NULL, $date = FALSE)
	{
		if (!$this->loaded()) return FALSE;

		$kupon_id = $this->id;

		$end_state_query = $this->get_balance_by_end_state($kupon_id, $state, $order_id);
		$begin_state_query = $this->get_balance_by_begin_state($kupon_id, $state, $order_id);

		if ($date) {
			$end_state_query = $end_state_query->where("date", "<", $date);
			$begin_state_query = $begin_state_query->where("date", "<", $date);
		}

		$balance = DB::select($end_state_query, $begin_state_query)
					->execute();

		return $balance->get("end_count") - $balance->get("begin_count");
	}


	function change_state($begin_state, $end_state, $order_id = NULL, $access_key = NULL, $description = NULL)
	{
		// if ($begin_state == self::SOLD) {
		// 	return FALSE;
		// }

		$kupon_id = $this->id;

		$om = ORM::factory('Object_Movement');
		$om->begin_state = $begin_state;
		$om->end_state = $end_state;
		$om->kupon_id = $kupon_id;
		$om->order_id = $order_id;
		$om->count = 1;
		$om->description = $description;
		$om->save();

		$this->access_key = $access_key;
		$this->order_id = $order_id;
		$this->state = $end_state;
		$this->save();

		return TRUE;
	}

	function check_available()
	{
		if (!$this->loaded()) return FALSE;

		if ($this->get_balance() > 0 ) {
			return TRUE;
		}

		return FALSE;
	}

	function check_and_restore_reserve_if_possible($access_key)
	{
		if (!$this->loaded()) return FALSE;

		if ($this->access_key == $access_key) {
			return TRUE;
		}

		if ($this->check_available())
		{
			$this->reserve(NULL, $access_key);
			return TRUE;
		}

		return FALSE;
	}

	function get_last_operation()
	{
		if (!$this->loaded()) return FALSE;

		return ORM::factory('Object_Movement')->where("kupon_id","=",$this->id)->order_by("date","desc")->find();
	}

	function get_avail($group_id)
	{
		return $this->where("kupon_group_id","=",$group_id)->where("state","IN", array(self::AVAIL));
	}

	function get_avail_count($group_id)
	{
		return $this->get_avail($group_id)->count_all();
	}

	function reserve($order_id = NULL, $access_key = NULL)
	{
		return $this->change_state($this->state, self::RESERVE, $order_id, $access_key);
	}

	function return_to_avail($description = NULL)
	{
		return $this->change_state($this->state, self::AVAIL, NULL, NULL, $description);
	}

	function to_sold($order_id)
	{
		return $this->change_state($this->state, self::SOLD, $order_id);
	}

}

