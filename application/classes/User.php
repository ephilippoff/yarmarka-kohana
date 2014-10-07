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
}



/* End of file User.php */
/* Location: ./application/classes/User.php */