<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Special controller for internal sub requests (HMVC)
 * 
 * @uses Controller
 * @uses _Template
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Controller_Block extends Controller_Template
{
	public function before()
	{
		// only sub requests in allowed
		if ($this->request->is_initial())
		{
			throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		parent::before();
	}
}
