<?php defined('SYSPATH') or die('No direct script access.');


class Task_TestEmail extends Minion_Task
{
	protected $_options = array();

	protected function _execute(array $params)
	{

		$objects = DB::select("o.*")
		                 ->from(array("object","o") )
		                 ->where("o.author","=",327190)
		                 ->limit(10)
		                 ->execute();
		$domain = 'http://c.yarmarka.biz';
		if (count($objects) AND $objects[0]['city_id'] == 1979) {
			$domain = 'http://surgut.yarmarka.biz';
		}

		 $msg = View::factory('emails/object_to_archive',
		         array(
		             'objects' => $objects,
		             'domain' => $domain
		         ))->render();

		Minion_CLI::write(
			Email::send('almaznv@yandex.ru', Kohana::$config->load('email.default_from'), 'Ваши объявления перемещены в архив', $msg)
		);

	}

}
