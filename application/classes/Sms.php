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
		
		if ($sms_pilot->send($number, $text))
		{
			$sms_record->set_success($sms_pilot->success);
		}
		else
		{
			$sms_record->set_error($sms_pilot->error);
		}
	}
}


/* End of file Sms.php */
/* Location: ./application/classes/Sms.php */