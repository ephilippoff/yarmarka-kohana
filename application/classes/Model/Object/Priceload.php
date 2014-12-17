<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Priceload extends ORM {
	
	protected $_table_name = 'object_priceload';

	protected $_belongs_to = array(
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
		'priceload_obj'	=> array('model' => 'Priceload', 'foreign_key' => 'priceload_id'),
	);
}

