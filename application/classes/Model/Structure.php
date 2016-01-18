<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Structure extends ORM {

	protected $_table_name = 'structure';

	protected $_has_many = array(
		'sub_structure' => array('model' => 'Structure', 'foreign_key' => 'parent_id'),
	);

} // End Ipblock Model
