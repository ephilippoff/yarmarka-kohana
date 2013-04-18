<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller_Admin_Template 
 * 
 * @uses Controller
 * @uses _Template
 * @abstract
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
abstract class Controller_Admin_Template extends Controller_Template {
	public $layout		= 'admin';

	/**
	 * Admin module name to check acceess rights
	 * 
	 * @var string
	 * @access protected
	 */
	protected $module_name;

    public function before()
	{
		$this->module_name = $this->module_name ? $this->module_name : $this->request->controller();

		if ( ! Auth::instance()->logged_in() AND ! ($this->request->action() == 'login'))
		{
			$this->redirect('khbackend/users/login');
		}
		elseif (Auth::instance()->logged_in() AND ! Auth::instance()->have_access($this->module_name))
		{
			throw new HTTP_Exception_404;
		}
		parent::before();
	}
} // End Admin_Template
