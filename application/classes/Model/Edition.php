<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Edition 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Edition extends ORM {

	protected $_belongs_to = array(
		'city' 	=> array(),
	);

} // End Model_Edition Model
