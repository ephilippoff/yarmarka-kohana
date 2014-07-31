<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_MassloadAvito extends Minion_Task
{
	protected $_options = array(
		'limit'	=> 1000,
		'category' => 3
	);

	protected function _execute(array $params)
	{
		$user 		=  ORM::factory('User', 327190);
		Minion_CLI::write('user role: '.$user->role);
		Auth::instance()->force_login($user);
		$limit 		= $params['limit'];
		$category 	= $params['category'];
		$offset 	= 0;

		$pathtofile = '/home/avagapov/WEB/yarmarka/yarmarka/uploads/111.xml';
		Minion_CLI::write('filexist:'.file_exists($pathtofile));
		

		$avito = new Massload_Avito;
		$avito->convert_file($pathtofile);


		//Minion_CLI::write('Start signature loading:'.Minion_CLI::color($total, 'cyan'));

		
	}



}
