<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Service_Outputs 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Service_Outputs extends ORM {

	protected $_table_name = 'service_outputs';

	protected $_belongs_to = array(
		'invoice'				=> array(),
		'user'					=> array(),
		'planningofnumber' 		=> array(),
	);
} // End Service_Outputs Model
