<?php if ( ! defined('SYSPATH')) exit('No direct script access allowed');

class Model_Dbdns extends Model_Database
{
	protected $_db = 'db_dns'; // use this db instance for every query in this model

	public function add_record($object_id)
	{
		if ( ! $object_id)
		{
			return FALSE;
		}
		
		$query = DB::insert('records', array('name'))->values(array($object_id.'ya24.biz'));

		return $query->execute($this->_db);
	}
}

/* End of file Dbdns.php */
/* Location: ./application/classes/Model/Dbdns.php */