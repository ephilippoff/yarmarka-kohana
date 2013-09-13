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
	public static function register($login, $email, $password)
	{
		$user = ORM::factory('User');
		$user->login 		= $login;
		$user->email 		= $email;
		$user->password 	= $password;
		$user->role 		= 2;
		$user->code 		= Text::random_string_hash($email);
		$user->is_blocked 	= 2;
		$user->ip 			= Request::$client_ip;
		$user->save();

		return $user;
	}
}

/* End of file User.php */
/* Location: ./application/classes/User.php */