<?php defined('SYSPATH') or die('No direct script access.');

class Text extends Kohana_Text {
	
	public static function plural_by_num($num, $word1, $word2, $word3)
	{
		$word = $word3;

		$last_1 = substr($num, -1);
		$last_2 = substr($num, -2);

		switch ($last_1) 
		{
			case 1:
				$word = $word1;
			break;
			case 2:
			case 3:
			case 4:
				$word = $word2;
			break;
		}

		switch ($last_2) 
		{
			case 11:
			case 12:
			case 13:
			case 14:
				$word = $word3;
			break;
		}

		return $word;
	}

	public static function ucfirst($string, $delimiter = '-')
	{
		return implode($delimiter, array_map(function($string){
			return mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string), 'UTF-8');
		}, explode($delimiter, $string)));
	}


	public static function highlight_word($word, $string, $start_tag = '<b>', $end_tag = '</b>')
	{
		return preg_replace('/('.$word.')/ui', $start_tag.'$1'.$end_tag, $string);
	}

	public static function clear_phone_number($contact)
	{
		return preg_replace('/[^0-9]*/', '', $contact);
	}

	public static function create_cache_key($string_part, $for_serialize = NULL)
	{
		return $string_part.'::'.sha1(print_r($for_serialize, TRUE));
	}

	public static function random_string_hash($str)
	{
		return sha1($str.microtime());
	}

	public static function format_phone($phone)
	{
		$phone = Text::clear_phone_number($phone);

		if (strpos($phone, '79') === 0)
		{
			// mobile phone
			return '+'.$phone[0].'('.substr($phone, 1, 3).')'.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9, 2);
		}
		else
		{
			// home phone
			return '+'.$phone[0].'('.substr($phone, 1, 4).')'.substr($phone, 5, 2).'-'.substr($phone, 7, 2).'-'.substr($phone, 9);
		}
	}
}
