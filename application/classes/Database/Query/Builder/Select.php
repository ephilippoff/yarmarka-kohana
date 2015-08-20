<?php defined('SYSPATH') or die('No direct script access.');

class Database_Query_Builder_Select extends Kohana_Database_Query_Builder_Select {
	// for correct working of count_all() by distinct
	public $_distinct;

	public function get_sql()
	{
		return $this->_sql;
	}
}
