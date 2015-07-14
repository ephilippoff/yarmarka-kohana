<?php defined('SYSPATH') OR die('No direct access');
/**
 * Kohana exception class. Translates exceptions using the [I18n] class.
 *
 * @package    Kohana
 * @category   Exceptions
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Exception_Withparams extends Kohana_Exception {

	public function __construct($message = "", $params = array(), $code = 0, Exception $previous = NULL)
	{
		// Pass the message and integer code to the parent
		parent::__construct($message, NULL, (int) $code, $previous);

		$this->params = $params;
	}

	public function getParams(){
		return $this->params;
	}
}
