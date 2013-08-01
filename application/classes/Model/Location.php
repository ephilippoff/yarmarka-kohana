<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Location 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Location extends ORM {

	protected $_has_many = array(
		'users' 	=> array(),
	);

	public function filters()
	{
		return array(
			TRUE => array(
				array('trim'),
			),
		);
	}
}

/* End of file Location.php */
/* Location: ./application/classes/Model/Location.php */