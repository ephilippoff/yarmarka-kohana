<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Sms extends ORM {

	const PENDING = 'PENDING';
	const ERROR = 'ERROR';
	const SUCCESS = 'SUCCESS';

	protected $_table_name = 'sms';

	public function set_success($response_text, $response = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if ($response) {
			$this->pilot_id = $response['id'];
			$this->status_code = $response['status'];
		}

		$this->response = $response_text;
		$this->status = self::SUCCESS;

		return $this->save();
	}

	public function set_error($response_text, $response = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}


		if ($response) {
			$this->pilot_id = $response['id'];
			$this->status_code = $response['status'];
		}


		$this->response = $response_text;
		$this->status = self::ERROR;

		return $this->save();
	}

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