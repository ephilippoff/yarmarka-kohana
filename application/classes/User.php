<?php defined('SYSPATH') OR die('No direct script access.');

class User {
	
	/**
	 * Регистрация пользователя
	 * письмо о регистрации высылается отдельно, т.к. зависит от того откуда вызывается регистрация
	 * 
	 * @param  string $login
	 * @param  string $email
	 * @param  string $password
	 * @return object
	 */
	public static function register($login, $email, $password, $fullname = NULL)
	{
		$user = ORM::factory('User');
		$user->fullname 	= $fullname;
		$user->login 		= $login;
		$user->email 		= $email;
		$user->passw 		= $password;
		$user->role 		= 2;
		$user->code 		= Text::random_string_hash($email);
		$user->is_blocked 	= 0;
		$user->ip_addr 		= Request::$client_ip;
		$user->save();

		return $user;
	}

	public static function generate_code($str)
	{
		return sha1($str.microtime());
	}

	public static function check_orginfo(ORM $user)
	{
		$date_new_registration = Kohana::$config->load("common.date_new_registration");
		if (strtotime($this->regdate) > strtotime($date_new_registration))
			HTTP::redirect("/user/orginfo?from=another");
		
		$date_expired = ORM::factory('User_Settings')
								->where("user_id","=",$user->id)
								->where("name","=","date-expired")
								->where("type","=","orginfo")
								->find();
		if (!$date_expired->loaded())
		{
			$date_expired->user_id = $user->id;
			$date_expired->type = "orginfo";
			$date_expired->name = "date-expired";
			$date = new DateTime();
			$date->add(date_interval_create_from_date_string('14 days'));
			$date_expired->value  = $date->format('Y-m-d H:i:s');
			$date_expired->save();

		}
		elseif ($date_expired->loaded())
		{
			$date = new DateTime();
			if (strtotime($date->format('Y-m-d H:i:s')) >= strtotime($date_expired->value))
			{
				HTTP::redirect("/user/orginfo?from=another");
			}
		}

	}
}



/* End of file User.php */
/* Location: ./application/classes/User.php */