<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact extends ORM {

	protected $_belongs_to = array(
		'contact_type' 		=> array('model' => 'Contact_Type', 'foreign_key' => 'contact_type_id'),
		'verified_user' 	=> array('model' => 'User', 'foreign_key' => 'verified_user_id'),
	);

	protected $_has_many = array(
		'users' 	=> array('model' => 'User', 'through' => 'user_contacts'),
		'objects' 	=> array('model' => 'Object', 'through' => 'object_contacts'),
	);

	public function filters()
	{
		return array(
			'contact_clear' => array(
				array('strtolower'),
			),
			'contact' => array(
				array('strtolower'),
			),
		);
	}

	public function where_user_id($user_id)
	{
		return $this->join_user_contacts()
			->where('user_contacts.user_id', '=', intval($user_id));
	}

	public function where_object_id($object_id)
	{
		return $this->join_object_contacts()
			->where('object_contacts.object_id', '=', intval($object_id));
	}

	public function join_object_contacts()
	{
		return $this->join('object_contacts')
			->on('object_contacts.contact_id', '=', 'contact.id');
	}

	public function join_user_contacts($value='')
	{
		return $this->join('user_contacts')
			->on('user_contacts.contact_id', '=', 'contact.id');
	}

	public function is_phone_unique($verified = FALSE)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$query = $this->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
			->where('id', '!=', $this->id)
			->where('contact_clear', '=', $this->contact_clear);

		if ($verified)
		{
			$query->where('verified_user_id', '!=', DB::expr('NULL'));
		}

		return ! (bool) $query->count_all();
	}

	public function by_value($value)
	{
		$value = trim(strtolower($value));
		if ( !Valid::email($value) ) {
			$value = Text::clear_phone_number($value);
			return $this->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
				->where('contact_clear', '=', $value);
		} else {
			return $this->where('contact_type_id', '=', Model_Contact_Type::EMAIL)
				->where('contact_clear', '=', $value);
		}
	}

	public function by_phone_number($contact_clear)
	{
		return $this->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
			->where('contact_clear', '=', Text::clear_phone_number($contact_clear));
	}

	public function by_contact_and_type($contact, $contact_type_id)
	{
		$contact 			= trim(strtolower($contact));
		$contact_type_id 	= intval($contact_type_id);

		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$this->by_phone_number($contact);
		}
		else
		{
			$this->where('contact_type_id', '=', $contact_type_id)
				->where('contact_clear', '=', $contact);
		}

		return $this;
	}

	public function is_phone()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return Model_Contact_Type::is_phone($this->contact_type_id);
	}

	/**
	 * Все контакты создаем через create, чтобы автоматически проверять на дубли
	 * 
	 * @param  object $validation
	 * @return object
	 */
	public function create(Validation $validation = NULL)
	{
		$exists_contact = ORM::factory('Contact')->by_contact_and_type($this->contact, $this->contact_type_id)
			->find();

		if ($exists_contact->loaded())
		{
			return $exists_contact;
		}
		
		if (Model_Contact_Type::is_phone($this->contact_type_id))
		{
			$this->contact_clear = Text::clear_phone_number($this->contact);
			$this->contact_type_id = (strpos($this->contact_clear, '79') === 0)
				? Model_Contact_Type::MOBILE : Model_Contact_Type::PHONE;
		}

		return parent::create($validation);
	}

	public function is_verified_for_session($session_id)
	{
		return (bool) ORM::factory('Verified_Contact')->where('contact_id', '=', $this->id)
			->where('session_id', '=', $session_id)
			->count_all();
	}

	public function is_verified($session_id)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if (Auth::instance()->get_user() AND $this->verified_user_id === Auth::instance()->get_user()->id)
		{
			return TRUE;
		}

		return $this->is_verified_for_session($session_id);
	}

	public function verify_for_session($session_id = NULL)
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if (is_null($session_id))
		{
			$session_id = session_id();
		}

		ORM::factory('Verified_Contact')
			->values(array('session_id' => $session_id, 'contact_id' => $this->id))
			->create();
	}

	public function get_contact_value()
	{
		return $this->is_phone() ? Text::format_phone($this->contact_clear) : trim($this->contact);
	}

	public function is_blocked()
	{
		return ($this->blocked) ? FALSE : TRUE;
	}
	
	public function increase_visits($object_id)
	{
		$object_contact = ORM::factory('Object_Contacts')
				->where('object_id', '=', $object_id)
				->where('contact_id', '=', $this->id)
				->find();

		if ($object_contact->loaded())
		{
			$object_contact->open_cnt++;
			$object_contact->save();
		}

		
	}

	public function check_verify_contact($session_id = NULL)
	{
		$session_id = ($session_id) ? $session_id : session_id();

		$validation = Validation::factory(array(
			"contact_".Model_Contact_Type::get_type_name($this->contact_type_id) => $this->contact_clear
		));

		$validation->rules('contact_mobile', array(
			array('mobile_verified', array(':value', $session_id, $this) )
		));

		$validation->rules('contact_phone', array(
			array('phone_verified', array(':value', $session_id, $this) )
		));

		$validation->rules('contact_email', array(
			array('email_verified', array(':value', $session_id, $this) )
		));

		return $validation;
	}

	public function check_contact($session_id = NULL, $contact_value = NULL, $type_id = NULL, $check_sms = FALSE, $correct = FALSE)
	{
		$session_id = ($session_id) ? $session_id : session_id();

		$type_id = $type_id;
		$type_name = Model_Contact_Type::get_type_name($type_id);
		$contact_value = $contact_value;

		if ($type_id <> Model_Contact_Type::EMAIL) {
			$contact_value = Text::clear_phone_number($contact_value);
		}

		$validation = Validation::factory(array(
			"contact_".$type_name => $contact_value
		));

		$contact_mobile_rules = array();
		$contact_phone_rules = array();
		$contact_email_rules = array();

		$contact_mobile_rules[] = array('is_mobile_contact', array(':value'));
		$contact_phone_rules[] = array('is_city_contact', array(':value'));
		$contact_email_rules[] = array('is_email_contact', array(':value'));

		if ($check_sms)
		{
			$contact_mobile_rules[] = array('mobile_sms_sessionmax', array(':value', $session_id));
			$contact_mobile_rules[] = array('mobile_sms_phonemax', array(':value', $session_id));
		}

		if ($this->loaded()) {

			$contact_mobile_rules[] = array('contact_blocked', array(':value', $this));
			$contact_phone_rules[] = array('contact_blocked', array(':value', $this));
			$contact_email_rules[] = array('contact_blocked', array(':value', $this));

			if ($this->verified_user_id)
			{
				$email = ORM::factory('User', $this->verified_user_id)->email;

				$contact_mobile_rules[] = array('contact_already_verified', array(':value', $this, $email));
				$contact_phone_rules[] = array('contact_already_verified', array(':value', $this, $email));
				$contact_email_rules[] = array('contact_already_verified', array(':value', $this, $email));
			}
		}

		$validation->rules("contact_mobile", $contact_mobile_rules);
		$validation->rules("contact_phone", $contact_phone_rules);
		$validation->rules("contact_email", $contact_email_rules);

		if ($this->loaded() AND $type_id <> $this->contact_type_id AND $correct) {
			$this->contact_type_id = $type_id;
			$this->save();
		}

		return $validation;
	}
}

/* End of file Contact.php */
/* Location: ./application/classes/Model/Contact.php */