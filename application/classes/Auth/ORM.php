<?php defined('SYSPATH') OR die('No direct access allowed.');

class Auth_ORM extends Kohana_Auth_ORM { 

	public function hash($str)
	{
		if ( ! $this->_config['hash_key'])
			throw new Kohana_Exception('A valid hash key must be set in your auth config.');

		return sha1($str . md5($str . $this->_config['hash_key']));
	}

	public function have_access($module)
	{
		// Get the user from the session
		$user = $this->get_user();

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
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('User');
			$user->where($user->unique_key($username), '=', $username)->find();
		}

		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		// If the passwords match, perform a login
		if ($user->passw === $password)
		{
			if ($remember === TRUE)
			{
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

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}
}
