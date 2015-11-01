<?php defined('SYSPATH') OR die('No direct access allowed.');

class Auth_ORM extends Kohana_Auth_ORM { 

	public function hash($str)
	{
		if ( ! $this->_config['hash_key'])
			throw new Kohana_Exception('A valid hash key must be set in your auth config.');

		return sha1($str . md5($str . $this->_config['hash_key']));
	}

	public function have_access_to($module)
	{
		// Get the user from the session
		$user = $this->get_user();

		if ($user AND $user->role == 1){
			return TRUE;
		}

		if ( ! $user)
			return FALSE;

		if ( ! is_object($module))
		{
			$module = ORM::factory('Module', array('class' => $module));
			if ( ! $module->loaded())
				return FALSE;
		}

		return $user->user_role->has('modules', $module);
	}

	public function logged_in($role = NULL)
	{
		// Get the user from the session
		$user = $this->get_user();

		if ( ! $user)
			return FALSE;

		if ($user instanceof Model_User AND $user->loaded())
		{
			// If we don't have a roll no further checking is needed
			if ( ! $role)
				return TRUE;

			if ( ! is_object($role))
			{
				// Load the role
				$role = ORM::factory('Role', array('name' => $role));

				if ( ! $role->loaded())
					return FALSE;
			}

			return $user->role->id === $role->id;
		}
	}

	public function trueforcelogin($user)
	{
		if (!is_object($user))
		{
			return FALSE;
		}

		$token = $this->create_token($user);

		// Set the autologin cookie
		Cookie::set('authautologin_s', $token->token, $this->_config['lifetime']);

		// Finish the login
		$this->complete_login($user);

		return TRUE;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   $username
	 * @param   string   $password
	 * @param   boolean  $remember  enable autologin
	 * @return  boolean
	 */
	protected function _login($user, $password, $remember)
	{
		$user = $this->check_user_by_password($user, $password);
		if ( ! $user)
		{
			return FALSE;
		}

		if ($remember === TRUE)
		{
			$token = $this->create_token($user);

			// Set the autologin cookie
			Cookie::set('authautologin_s', $token->token, $this->_config['lifetime']);
		}

		// Finish the login
		$this->complete_login($user);

		return TRUE;
	}

	public function check_user_by_password($user, $password)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('User');
			$user->get_user_by_email($username)->find();
		}

		if ($user->loaded() AND $user->is_blocked == 1)
		{
			$block_reason = "";
			if ($user->block_reason)
				$block_reason = "Причина: ".$user->block_reason;
			throw new Exception("Учетная запись заблокирована. ".$block_reason, 303);
		}

		if ($user->loaded() AND $user->is_blocked == 2)
		{
			$block_reason = "Учетная запись еще не активирована. Для активации перейдите по ссылке высланной вам ранее на email";
			throw new Exception($block_reason, 303);
		}

		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		// If the passwords match, perform a login
		if ($user->loaded() AND $user->passw === $password)
		{
			$this->check_user($user);
		}
		else
		{
			throw new Exception("Неверное сочетание логина и пароля.", 303);
		}

		return $user->loaded() ? $user : FALSE;
	}

	public function check_user($user)
	{
		if ( ! $user OR ! $user->loaded())
		{
			return FALSE;
		}

		if ($user->is_blocked == 1)
		{
			if ($user->block_reason)
			{
				throw new Exception("Ваша учетная запись заблокирована. Причина: ".$user->block_reason,301);
			}
			else
			{
				throw new Exception("Ваша учетная запись заблокирована.",302);
			}
		}
		elseif ($user->is_blocked == 2)
		{
			throw new Exception("Ваша учетная запись не активирована.", 300);
		} 

		return TRUE;
	}

	public function create_token(Model_User $user)
	{
		if ( ! $user->loaded())
		{
			return FALSE;
		}
		// Token data
		$data = array(
			'user_id'    => $user->pk(),
			'expires'    => time() + $this->_config['lifetime'],
			'user_agent' => sha1(Request::$user_agent),
		);

		// Create a new autologin token
		$token = ORM::factory('User_Token')
					->values($data)
					->create();

		return $token;
	}
}
