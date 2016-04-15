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

		 $msg = View::factory('emails/object_to_archive',
		         array(
		             'objects' => $objects
		         ))->render();

		Minion_CLI::write(
			Email::send('almaznv@yandex.ru', Kohana::$config->load('email.default_from'), 'Ваши объявления перемещены в архив', $msg)
		);

	}

}
