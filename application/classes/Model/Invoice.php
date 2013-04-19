<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Invoice 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Invoice extends ORM {

	const CREATED = 0;
	const SUCCESS = 2;
	const REFUSED = 3;

	protected $_belongs_to = array(
		'user' => array(),
	);

	public function created()
	{
		return $this->where('state', '=', self::CREATED);
	}

	public function success()
	{
		return $this->where('state', '=', self::SUCCESS);
	}

	public function refused()
	{
		return $this->where('state', '=', self::REFUSED);
	}

} // End Invoice Model
