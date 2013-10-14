<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_Units extends ORM {

	protected $_table_name = 'user_units';

	protected $_belongs_to = array(
		'user' 	=> array('model' => 'User', 'foreign_key' => 'user_id'),
		'unit' 	=> array('model' => 'Unit', 'foreign_key' => 'unit_id'),
		'location' => array('model' => 'Location', 'foreign_key' => 'locations_id'),
	);

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 250)),
			),			
		);
	}
	
	public function get_address(){
		$address = $this->location;
		if ($address)
			return $address->city.', '.$address->address;
		return 'нет адреса';
	}

} // End User_Messages Model

