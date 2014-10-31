<?php defined('SYSPATH') or die('No direct script access.');


class Task_Repairusers extends Minion_Task
{
	protected $_options = array(
		"email" => NULL,

	);

	protected function _execute(array $params)
	{
		$email = $params["email"];

		$sql = 'select email,cnt from (select w_lower(email) as email, count(*) as cnt from "user"'.
				" where fax is null or fax <> '*' ".
				'group by w_lower(email)
				order by cnt desc) as users
				where ';

		if ($email)
			$sql .= " email = :email";
		else 
			$sql .= " users.cnt > 1";

		$sql .= " and email is not null and email <> '' ";
		$query = DB::query(Database::SELECT, $sql);

		if ($email)
			$query = $query->param(':email', $email);
			
		$emails = $query->as_object()->execute();


		foreach ($emails as $doubleemail) {
			$doubles = ORM::factory('User')
						->where("email", "=", $doubleemail->email)
						->order_by("last_visit_date","desc NULLS last")
						->find_all();

			$info = new Obj();
			foreach ($doubles as $double)
			{
				
				if (!$info->main_account)
				{
					$info->main_account = $double->id;
				}

				$this->echoInfo($double);
			}


			foreach ($doubles as $user)
			{
				ORM::factory('Object')
					->set("author",$info->main_account)
					->set("author_company_id",$info->main_account)
					->where("author", "=", $user->id)->update_all();

				ORM::factory('Invoices')
					->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('Service_Invoices')
					->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('Contact')
					->set("verified_user_id",$info->main_account)
					->where("verified_user_id", "=", $user->id)->update_all();

				ORM::factory('Subscriptions')
					->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				$query = DB::select()->from('user_contacts')
				 	->select("contact_id")
					->where("user_id", "=", $info->main_account);

				 ORM::factory('User_Contact')
				 	->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)
					->where("contact_id","NOT IN ",$query)
					->update_all();

				ORM::factory('User_Units')
					->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('User_Messages')
				 	->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('User_Types')
					->set("parent_id",$info->main_account)
					->where("parent_id", "=", $user->id)->update_all();

				ORM::factory('Service_User')
				 	->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('User_Token')
					->set("user_id",$info->main_account)
					->where("user_id", "=", $user->id)->update_all();

				ORM::factory('Favourite')
					->set("userid",$info->main_account)
					->where("userid", "=", $user->id)->update_all();

				if ($info->main_account <> $user->id)
				{
					ORM::factory('User')
						->set("email", "__".$user->email)
					 	->set("is_blocked", 2)
					 	->set("fax", "*")
						->where("id", "=", $user->id)->update_all();
				}

			}

			$mainuser = ORM::factory('User', $info->main_account);

			Minion_CLI::write('main_account: '.$info->main_account);
			$this->echoInfo($mainuser);

			


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

		Minion_CLI::write('result: id='.$user->id.'  e='.$user->email.' o='.$user->org_name.' fio='.$user->fullname.' bl='.$user->is_blocked.' lvd='.$user->last_visit_date.' o='.$count_object.' i='.$count_invoice.' si='.$count_servinvoice.' con='.$count_contacts.' sub='.$count_subscriptions.' uc='.$count_usercontacts.' unit='.$count_unit.' um='.$count_um.' ut='.$count_ut.' su='.$count_serviuser.' ut='.$count_usertoken.' fv='.$count_favorite);

	}

}
