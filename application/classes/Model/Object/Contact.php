<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Contact extends ORM {

	protected $_table_name = 'object_contact';

	protected $_belongs_to = array(
		'contact_type' 	=> array('model' => 'Contact_Type', 'foreign_key' => 'contact_type_id'),
		'user'			=> array(),
		'object'		=> array(),
	);

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

	public function get_by_phone_number($contact_clear)
	{
		return $this->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE))
			->where('contact_clear', '=', Text::clear_phone_number($contact_clear));
	}

	public function is_phone()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return Model_Contact_Type::is_phone($this->contact_type_id);
	}
}

/* End of file Contact.php */
/* Location: ./application/classes/Model/Object/Contact.php */