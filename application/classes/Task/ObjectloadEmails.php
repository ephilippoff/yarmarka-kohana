<?php defined('SYSPATH') or die('No direct script access.');


class Task_ObjectloadEmails extends Minion_Task
{

	protected $_options = array(
		"email"  => FALSE
	);

	protected function _execute(array $params)
	{
		$direct_email 			= $params['email'];

		$users =	ORM::factory('User')
						->join('user_settings')
							->on('user_settings.user_id','=','user.id')
						->where('user_settings.name', '=', 'massload_email')
						->find_and_map(function($row){
							return array(
								'id'=>$row->id,
								'email'=>$row->email
							);
						});

		
		foreach ($users as $user) {
			
			$ol = ORM::factory('Objectload')
					->where('user_id', '=', $user['id'])
					->where('state','=', 5)
					->order_by('created_on','desc')
					->find();

			if ($ol->loaded()) {

				$ol = new Objectload($user['id'], $ol->id);

				$emails = $ol->sendReport(NULL, $direct_email );

				Minion::write("Send report", Debug::vars($emails));

			}

		}

	}

}