<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object extends ORM {

	protected $_table_name = 'object';

	protected $_belongs_to = array(
		'user' => array('model' => 'User', 'foreign_key' => 'author'),
	);

} // End Access Model
