<?php defined('SYSPATH') OR die('No direct script access.');

class Date extends Kohana_Date {


	static function get_months_names() {
		return array(
            "01" => "января",
            "02" => "февраля",
            "03" => "марта",
            "04" => "апреля",
            "05" => "мая",
            "06" => "июня",
            "07" => "июля",
            "08" => "августа",
            "09" => "сентября",
            "10" => "октября",
            "11" => "ноября",
            "11" => "декабря"
        );
	}
}
