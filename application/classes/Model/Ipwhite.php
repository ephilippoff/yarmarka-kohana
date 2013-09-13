<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Ipwhite extends ORM {

	protected $_table_name = 'ip_white';

	public function get_by_ip($ip)
	{
		return $this->where('ip', '=', $ip)
			->find();
	}
}

/* End of file Ipwhite.php */
/* Location: ./application/classes/Model/Ipwhite.php */