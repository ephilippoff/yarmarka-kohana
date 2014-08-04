<?php defined('SYSPATH') or die('No direct script access.');

class Num extends Kohana_Num {

	public static function price($price)
	{
		return number_format($price, 0 , ',', ' ');
	}

	public static function rus_suffix($word, $number)
	{
		$return = $word;
		$suffix = FALSE;
		$number = $number."";
		switch ($number) {
			case "11":
			case "12":
			case "13":
			case "14":
			case "15":
			case "16":
			case "17":
			case "18":
			case "19":
				$suffix = "й";
			break;
		}
		$number = substr($number, strlen($number)-1);
		if(!$suffix){
			switch ($number) {
				case "1":
					$suffix = "е";
				break;
				case "2":
				case "3":
				case "4":
					$suffix = "я";
				break;
				default:
					$suffix = "й";
				break;
			}
		}
		if ($suffix)
			$return = substr_replace($word, $suffix, strlen($word)-2);
		return $return;
	}
}
