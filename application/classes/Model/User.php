<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User {

	protected $_table_name = 'user';

	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(	
		'user_tokens'	=> array('model' => 'User_Token'),
		'objects'		=> array('foreign_key' => 'author'),
		'access'		=> array('model' => 'Access'),
		'invoices'		=> array(),
		'subscriptions'	=> array(),
		'user_messages' => array('model' => 'User_Messages', 'foreign_key' => 'user_id'),
		'contacts'		=> array('model' => 'Contact', 'through' => 'user_contacts'),
		'link_requests' => array('model' => 'User_Link_Request', 'foreign_key' => 'linked_user_id'),
		'users'			=> array('model' => 'User', 'foreign_key' => 'linked_to_user'),		
		'units' 		=> array('model' => 'User_Units', 'foreign_key' => 'user_id'),
	);

	protected $_belongs_to = array(
		'user_role' 	=> array('model' => 'Role', 'foreign_key' => 'role'),
		'user_city'		=> array('model' => 'City', 'foreign_key' => 'city_id'),
		'user_type'		=> array('model' => 'User_Types', 'foreign_key' => 'org_type'),
		'location' 		=> array('model' => 'Location', 'foreign_key' => 'location_id'),
		'linked_to'		=> array('model' => 'User', 'foreign_key' => 'linked_to_user'),
	);

	/**
	 * Rules for the user model. Because the password is _always_ a hash
	 * when it's set,you need to run an additional not_empty rule in your controller
	 * to make sure you didn't hash an empty string. The password rules
	 * should be enforced outside the model or with a model helper method.
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'login' => array(
				array('max_length', array(':value', 32)),
				array(array($this, 'unique'), array('login', ':value')),
				array(array($this, 'check_ip')),
				array(array($this, 'check_cookie')),
			),
			'passw' => array(
				array('not_empty'),
			),
			'email' => array(
				array('email'),
				// array(array($this, 'unique'), array('email', ':value')),
				// array(array($this, 'check_domain'), array(':value')),
			),
			'role' => array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Filters to run when data is set in this model. The password filter
	 * automatically hashes the password when it's set in the model.
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'passw' => array(
				array(array(Auth::instance(), 'hash'))
			),
			'email' => array(
				array(array($this, 'trigger_save_email'), array(':value')),
			),
			'phone' => array(
				array(array($this, 'trigger_save_phone'), array(':value')),
			),
		);
	}

	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels()
	{
		return array(
			'username'         => 'username',
			'email'            => 'email address',
			'password'         => 'password',
		);
	}

	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'login';
	}

	public function complete_login()
	{
		if ($this->_loaded)
		{
			$access = ORM::factory('Access');
			$access->user_id	= $this->id;
			$access->ip			= Request::$client_ip;
			$access->save();
		}
	}

	public function save(Validation $validation = NULL)
	{
		$this->_table_name = 'user';
		parent::save($validation);
	}

	public function update(Validation $validation = NULL)
	{
		$this->_table_name = 'user';
		parent::update($validation);
	}

	public function delete()
	{
		$this->_table_name = 'user';
		parent::delete();
	}

	public function get_hash()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return sha1($this->login.$this->passw.'secret_##42');
	}

	/**
	 * User contacts
	 *
	 * @param  int/array $type
	 * @return object Database_Result object
	 */
	public function get_contacts($type = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$object_contact = ORM::factory('Contact');

		$query = $object_contact->select('contact_type.name')
			->with('contact_type')
			->where_user_id($this->id)
			->order_by('id');

		if ($type)
		{
			if (is_array($type))
			{
				$query->where('contact_type_id', 'IN', $type);
			}
			else
			{
				$query->where('contact_type_id', '=', intval($type));
			}
		}


		return $query->find_all();
	}

	public function add_contact($contact_type_id, $contact_str, $moderate = 0)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$contact = ORM::factory('Contact');
		$contact->contact_type_id	= intval($contact_type_id);
		$contact->contact			= trim($contact_str);
		$contact->moderate 			= intval($moderate);
		$contact = $contact->create();

		if ( ! $contact->has('users', $this->id))
		{
			$contact->add('users', $this->id);
		}

		return $contact;
	}

	public function add_verified_contact($contact_type_id, $contact_str, $moderate = 0)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$contact_clear = Text::clear_phone_number($contact_str);
		}
		else
		{
			$contact_clear = trim($contact_str);
		}

		// create contact if not exists
		$contact = $this->add_contact($contact_type_id, $contact_str, $moderate);
		// remove contact from other users
		DB::delete('user_contacts')
			->where('contact_id', '=', $contact->id)
			->where('user_id', '!=', $this->id)
			->execute();
		// unpublish objects with that contact
		if ($this->id != $contact->verified_user_id)
		{
			$objects = ORM::factory('Object')
				->join('object_contacts')
				->on('object.id', '=', 'object_contacts.object_id')
				->where('contact_id', '=', $contact->id)
				->find_all();

			foreach ($objects as $object)
			{
				$object->is_published = 0;
				$object->save();

				$object->remove('contacts', $contact);
			}
		}
		// set contact verified for current user
		$contact->verified_user_id = $this->id;
		$contact->save();

		return $contact;
	}

	public function delete_contact($contact_id)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$contact = ORM::factory('Contact');
		$contact->where_user_id($this->id)
			->where('contact.id', '=', intval($contact_id))
			->find();
		if ($contact->loaded())
		{
			$this->remove('contacts', $contact);
			return TRUE;
		}

		return FALSE;
	}

	public function get_user_name()
	{
		if (trim($this->fullname))
			return $this->fullname;

		if (trim($this->login))
			return $this->login;

		return $this->email;
	}

	public function can_edit_object($object_id)
	{
		$object = ORM::factory('Object', $object_id);
		if ( ! $object->loaded() OR ! $this->loaded())
		{
			return FALSE;
		}

		$is_white_ip 	= ORM::factory('Ipwhite')->get_by_ip(Request::$client_ip)->loaded();
		$is_author 		= ($this->id === $object->author);
		$is_admin 		= (($this->role == 1 OR $this->role == 3) AND $is_white_ip);
		$is_company 	= ($this->id === $object->author_company_id);

		return ($is_author OR $is_admin OR $is_company);
	}

	public function check_domain($email)
	{
		if ( ! $email)
		{
			return TRUE;
		}
		
		$disallowed_domains = Kohana::$config->load('common.disallowed_email_domains');
		list($email_name, $domain) = explode('@', $email);

		return ! in_array($domain, $disallowed_domains);
	}

	public function check_ip()
	{
		$user_by_ip 	= ORM::factory('User')->where('ip_addr', '=', Request::$client_ip)->find();
		$is_white_ip 	= ORM::factory('Ipwhite')->get_by_ip(Request::$client_ip)->loaded();

		if ($user_by_ip->loaded() AND ! $is_white_ip AND ceil((time() - strtotime($user_by_ip->regdate))/Date::HOUR) <= 12)
		{
			return FALSE;
		}

		return TRUE;
	}

	public function check_cookie()
	{
		if ($user_id = Cookie::get('r_user_id'))
		{
			$user = ORM::factory('User', $user_id);
			if ($user->loaded())
			{
				return FALSE; // пользователь уже регистрирвоался с этого браузера
			}
		}

		return TRUE;
	}
	
	public function getAllUnits()
	{
		return $this->units->find_all()->as_array();
	}

	public function count_company_objects($company_id)
	{
		return $this->objects->where('author_company_id', '=', $company_id)
			->count_all();
	}

	public function trigger_save_email($email)
	{
		if ($email AND Valid::email($email))
		{
			$contact = ORM::factory('Contact');
			$contact->contact 			= trim($email);
			$contact->contact_type_id 	= Model_Contact_Type::EMAIL;
			$contact->create();
		}

		return $email;
	}

	public function trigger_save_phone($phone)
	{
		if ($phone)
		{
			$contact = ORM::factory('Contact');
			$contact->contact 			= trim($phone);
			$contact->contact_type_id 	= Model_Contact_Type::MOBILE;
			$contact->create();
		}

		return $phone;
	}
}

/* End of file User.php */
/* Location: ./application/classes/Model/User.php */