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
	);

	protected $_belongs_to = array(
		'user_role' => array('model' => 'Role', 'foreign_key' => 'role'),
		'user_city'	=> array('model' => 'City', 'foreign_key' => 'city_id'),
		'user_type'	=> array('model' => 'User_Types', 'foreign_key' => 'org_type'),
		'location' 	=> array(),
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
			),
			'passw' => array(
				array('not_empty'),
			),
			'email' => array(
				array('email'),
				array(array($this, 'unique'), array('email', ':value')),
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
			'password' => array(
				array(array(Auth::instance(), 'hash'))
			)
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

		$object_contact = ORM::factory('Object_Contact');

		$query = $object_contact->select('contact_type.name')
			->join('contact_type')
			->on('contact_type.id', '=', 'object_contact.contact_type_id')
			->where('user_id', '=', $this->id)
			->where('object_id', 'IS', DB::expr('NULL'))
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

	public function add_contact($contact_type_id, $contact_str)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$unique = ! (bool) ORM::factory('Object_Contact')
			->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
			->where('contact_clear', '=', Text::clear_phone_number($contact_str))
			->count_all();

		$contact = ORM::factory('Object_Contact');
		$contact->contact_type_id	= intval($contact_type_id);
		$contact->contact			= trim($contact_str);
		$contact->user_id			= $this->id;
		$contact->verified			= ($unique ? 0 : -1);
		$contact->save();

		return $contact;
	}

	public function delete_contact($contact_id)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$contact = ORM::factory('Object_Contact');
		$contact->where('user_id', '=', $this->id)
			->where('id', '=', intval($contact_id))
			->find();
		if ($contact->loaded())
		{
			$contact->delete();
			return TRUE;
		}

		return FALSE;
	}

	public function get_user_name()
	{
		if ($this->fullname)
			return $this->fullname;

		if ($this->login)
			return $this->login;

		return $this->email;
	}
}

/* End of file User.php */
/* Location: ./application/classes/Model/User.php */