<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Subscription 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Subscription extends ORM {

	protected $_belongs_to = array(
		'user' => array(),
	);
	/**
	 * Preformat period frontend text
	 * 
	 * @access public
	 * @return string
	 */
	public function get_period()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		if ($this->period < 24 OR ($this->period > 24 AND $this->period%24 != 0))
		{
			return $this->period.' '.Text::plural_by_num($this->period, 'час', 'часа', 'часов');
		}
		else
		{
			return ($this->period/24).' '.Text::plural_by_num($this->period/24, 'день', 'дня', 'дней');
		}
	}
} // End Subscription Model
