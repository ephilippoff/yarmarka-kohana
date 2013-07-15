<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Contact extends ORM {

	protected $_table_name = 'object_contact';

	protected $_belongs_to = array(
		'contact_type' => array('model' => 'Contact_Type', 'foreign_key' => 'contact_type_id'),
	);

} // End Object_Contact Model
