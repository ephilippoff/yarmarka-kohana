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
		$user->is_blocked 	= 0; // @todo так блокируем или нет при регистрации?
		$user->ip_addr 		= Request::$client_ip;
		$user->save();

		// из-за триггеров last id возвращает не верный, поэтому перегружаем объект из базы по email
		$user = ORM::factory('User')->where('email', '=', $user->email)->find();

		return $user;
	}
}

/* End of file User.php */
/* Location: ./application/classes/User.php */