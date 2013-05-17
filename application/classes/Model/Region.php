<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Region 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Region extends ORM {

	protected $_table_name = 'region_new';

	protected $_has_many = array(
		'cities'	=> array(),
	);

} // End Region Model
