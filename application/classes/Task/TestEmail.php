<?php defined('SYSPATH') or die('No direct script access.');


class Task_TestEmail extends Minion_Task
{
	protected $_options = array();

	protected function _execute(array $params)
	{

		Minion_CLI::write(Email::send( 'almaznv@yandex.ru', Kohana::$config->load('email.default_from'), 'test', '	Осенью, планируя свое участие в очередном избирательном цикле, мы уже спрашивали вас о вашем отношении к предстоящим выборам и нашему участию в них. За прошедшие полгода много чего случилось, и мы хотели бы получить новые социологические данные и посмотреть, есть ли сдвиги в настроениях наших сторонников.'));

	}

}
