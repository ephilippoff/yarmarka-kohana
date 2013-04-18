<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Access 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Access extends ORM {

	protected $_table_name = 'access';

	protected $_belongs_to = array(
		'user' => array(),
	);

} // End Access Model
