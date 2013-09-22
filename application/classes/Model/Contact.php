<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Contact extends ORM {

	protected $_belongs_to = array(
		'contact_type' 	=> array('model' => 'Contact_Type', 'foreign_key' => 'contact_type_id'),
	);

	protected $_has_many = array(
		'users' 	=> array('model' => 'User', 'through' => 'user_contacts'),
		'objects' 	=> array('model' => 'Object', 'through' => 'object_contacts'),
	);

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
			$query->where('verified', '=', 1);
		}

		return ! (bool) $query->count_all();
	}

	public function by_phone_number($contact_clear)
	{
		return $this->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
			->where('contact_clear', '=', Text::clear_phone_number($contact_clear));
	}

	public function by_contact_and_type($contact, $contact_type_id)
	{
		$contact 			= trim($contact);
		$contact_type_id 	= intval($contact_type_id);

		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$this->by_phone_number($contact);
		}
		else
		{
			$this->where('contact', '=', $contact);
		}

		return $this->where('contact_type_id', '=', $contact_type_id);
	}

	public function is_phone()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return Model_Contact_Type::is_phone($this->contact_type_id);
	}

	public function create(Validation $validation = NULL)
	{
		if (Model_Contact_Type::is_phone($this->contact_type_id))
		{
			$this->contact_clear = Text::clear_phone_number($this->contact);
			$unique = ! (bool) ORM::factory('Contact')
				->where('contact_type_id', '=', $this->contact_type_id)
				->where('contact_clear', '=', $this->contact_clear)
				->count_all();

			if ($unique)
			{
				$this->verified = 1;
			}
		}

		return parent::create($validation);
	}
}

/* End of file Contact.php */
/* Location: ./application/classes/Model/Contact.php */