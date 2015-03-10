<?php defined('SYSPATH') or die('No direct script access.');

class Task_DeleteUsers extends Minion_Task
{
	protected $_options = array(
		'limit' => 10,
	);

	protected function _execute(array $params)
	{
		$limit 	= $params['limit'];

		Minion_CLI::write('Delete inactive users (is_blocked = 2)');
		$users = ORM::factory('User')
			->where('is_blocked', '=', 2)
			->where('email', 'IS NOT', NULL)
			->where('regdate','<',DB::expr("NOW() - interval '14 days'"))
			->order_by("regdate", "desc")
			->limit($limit)
			->find_all();

		Minion_CLI::write('Affected rows:'.Minion_CLI::color($users->count(), 'cyan'));
		foreach ($users as $user)
		{
				Minion_CLI::write($user->email." - ".$user->regdate);
				if ($user->filename) {
					foreach (Imageci::getSitePaths($user->filename) as $key => $filename) {
						if (file_exists("./".$filename)) {
							Minion_CLI::write("deleted - ".$filename);
							unlink("./".$filename);
							if (file_exists("./".$filename)) {
								Minion_CLI::write('!!!!!!!!!!!!!!!!!!!!!!!!!');
							}
						}
					}
				}
			$this->echoInfo($user);
			$user->delete();
		}

	}

	function echoInfo($user)
	{
		$count_object = ORM::factory('Object')
										->where("author", "=", $user->id)->count_all();
		$count_invoice = ORM::factory('Invoices')
								->where("user_id", "=", $user->id)->count_all();
		$count_servinvoice = ORM::factory('Service_Invoices')
								->where("user_id", "=", $user->id)->count_all();
		$count_contacts = ORM::factory('Contact')
							->where("verified_user_id", "=", $user->id)->count_all();
		$count_subscriptions = ORM::factory('Subscriptions')
								->where("user_id", "=", $user->id)->count_all();
		$count_usercontacts = ORM::factory('User_Contact')
								->where("user_id", "=", $user->id)->count_all();
		$count_unit = ORM::factory('User_Units')
								->where("user_id", "=", $user->id)->count_all();
		$count_um = ORM::factory('User_Messages')
								->where("user_id", "=", $user->id)->count_all();
		$count_ut = ORM::factory('User_Types')
								->where("parent_id", "=", $user->id)->count_all();
		$count_serviuser = ORM::factory('Service_User')
								->where("user_id", "=", $user->id)->count_all();
		$count_usertoken = ORM::factory('User_Token')
								->where("user_id", "=", $user->id)->count_all();

		$count_favorite = ORM::factory('Favourite')
								->where("userid", "=", $user->id)->count_all();

		Minion_CLI::write('result: id='.$user->id.'  e='.$user->email.' bl='.$user->is_blocked.' lvd='.$user->last_visit_date.' o='.$count_object.' i='.$count_invoice.' si='.$count_servinvoice.' con='.$count_contacts.' sub='.$count_subscriptions.' uc='.$count_usercontacts.' unit='.$count_unit.' um='.$count_um.' ut='.$count_ut.' su='.$count_serviuser.' ut='.$count_usertoken.' fv='.$count_favorite);

	}
}