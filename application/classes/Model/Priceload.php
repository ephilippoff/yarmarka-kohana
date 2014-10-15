<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Priceload extends ORM {

	protected $_table_name = 'priceload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

}