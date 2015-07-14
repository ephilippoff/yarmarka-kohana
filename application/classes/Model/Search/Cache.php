<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search_Cache extends ORM {

	protected $_table_name = 'search_cache';

	public function get_query_by_hash($hash) {
		return $this->where("hash","=",$hash);
	}

	public function get_result_by_sql($sql, $order = FALSE, $order_direction = "ASC", $limit = 5, $exclusion = array()) {
		$result = $cleandexclusion = array();

		foreach ($exclusion as $exclusion_item) {
			array_push($cleandexclusion, intval($exclusion_item));
		}

		$query_sql = $sql;

		$result = DB::query(Database::SELECT, "select o.* " . $query_sql. " :exclusion :order :limit")
					->param(':limit', DB::expr("LIMIT ".$limit) );
		
		if ($order) {
			$result = $result->param(':order', DB::expr("ORDER BY ".$order." ".$order_direction));
		} else {
			$result = $result->param(':order', DB::expr(""));
		}

		if (count($cleandexclusion) > 0) {
			$result = $result->param(':exclusion', DB::expr("AND o.id NOT IN (".implode(",", $cleandexclusion).")"));
		} else {
			$result = $result->param(':exclusion', DB::expr(""));
		}

		$result = $result->execute();

		return $result;
	}
}

