<?php defined('SYSPATH') or die('No direct script access.');

class Sms
{
	public static function send($number, $text, $session_id = NULL)
	{
		$number = Text::clear_phone_number($number);
		$text 	= trim($text);
		if (is_null($session_id))
		{
			$session_id = session_id();
		}

		$text = Text::rus2translit($text);
		
		$sms_record = ORM::factory('Sms');
		$sms_record->phone 		= $number;
		$sms_record->text 		= $text;
		$sms_record->session_id = $session_id;
		$sms_record->status 	= Model_Sms::PENDING;
		$sms_record->save();

		$sms_pilot = new Smspilot(Kohana::$config->load('sms.api_key'), FALSE, Kohana::$config->load('sms.from'));
		$result = FALSE;

		$last_sms = ORM::factory('Sms')
			->where('phone','=',$number)
			->where('pilot_id','IS NOT', DB::expr('NULL'))
			->order_by("id","DESC")->find();

		//if last sms was sent with error, we will selecting reserve operator
		if ($last_sms->loaded()) {
			$last_sms_response = $sms_pilot->check($last_sms->pilot_id); //93464050
			if ($last_sms_response) {

				$last_sms_response = reset($last_sms_response);

				if ((int) $last_sms_response["status"] < 0) {
					$last_sms->status_code = $last_sms_response["status"];
					$last_sms->save();

					$sms_pilot = new Smspilot(Kohana::$config->load('sms.api_key'), FALSE, Kohana::$config->load('sms.from_reserve'));

				}

			}

		}

		$sms_request = $sms_pilot->send($number, $text);

		if ($sms_request)
		{
			$sms_response = reset($sms_request);

			if ($sms_response) {

				if ($sms_response['status'] == 0) {
					$result = $sms_record->set_success($sms_pilot->success, $sms_response);
				} else {
					$result = $sms_record->set_error($sms_pilot->error, $sms_response);
				}

			} else {

				$result = $sms_record->set_error($sms_pilot->error);

			}

		}
		else
		{
			$result = $sms_record->set_error($sms_pilot->error);
		}

		return $result;
	}
}


/* End of file Sms.php */
/* Location: ./application/classes/Sms.php */