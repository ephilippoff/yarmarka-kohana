<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Contact extends ORM
{
	protected $_table_name = 'object_contacts';

	protected $_belongs_to = array(
		'contact_obj'	=> array('model' => 'Contact', 'foreign_key' => 'contact_id'),
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
	);

	function compare($_input_contacts)
	{
		$existed_contacts = array();
		$input_contacts  = array();

		foreach ($this->find_all() as $contact) {
			$existed_contacts[] = $contact->contact_obj->contact;
		}

		foreach ($_input_contacts as $contact) {
			$input_contacts[] = $contact["value"];
		}

		asort($existed_contacts);
		asort($input_contacts);
		return !(count($existed_contacts) == count($input_contacts)
					&& count($existed_contacts) > 0 && count($input_contacts) > 0
						&& implode(",", $existed_contacts) == implode(",", $input_contacts));
	}

}

/* End of file Union.php */
/* Location: ./application/classes/Model/Object/Union.php */