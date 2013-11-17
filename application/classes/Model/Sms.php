<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Sms extends ORM {

	protected $_table_name = 'sms';

	public function cnt_by_phone($phone, $session_id = NULL)
	{
		if (is_null($session_id))
		{
			$session_id = session_id();
		}

		return $this->where('phone', '=', $phone)
			->where('session_id', '=', $session_id)
			->count_all();
	}

	public function cnt_by_session_id($session_id = NULL)
	{
		if (is_null($session_id))
		{
			$session_id = session_id();
		}

		return $this->where('session_id', '=', $session_id)
			->count_all();
	}
}

/* End of file Sms.php */
/* Location: ./application/classes/Model/Sms.php */