<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Planningofnumber 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Planningofnumber extends ORM {

	protected $_table_name = 'planningofnumber';

	protected $_belongs_to = array(
		'edition' 	=> array('model' => 'Edition', 'foreign_key' => 'edition_code', 'local_key' => 'code'),
	);

} // End Model_Planningofnumber Model
