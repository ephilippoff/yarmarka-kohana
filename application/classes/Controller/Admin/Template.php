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
		parent::before();
		$this->module_name = $this->module_name ? $this->module_name : strtolower($this->request->controller());

		if ( ! Auth::instance()->logged_in() AND ! ($this->request->action() == 'login'))
		{
			$this->redirect('khbackend/users/login');
		}
		elseif (Auth::instance()->logged_in() AND $this->module_name != 'welcome' AND ! Auth::instance()->have_access_to($this->module_name))
		{
			throw new HTTP_Exception_404;
		}
		
		if (Auth::instance()->logged_in() AND Auth::instance()->get_user()->role == 2)
		{
			throw new HTTP_Exception_404;
		}			

		if (is_object($this->template))
		{
			$this->template->set_global('module_name', $this->module_name);
		}
	}
} // End Admin_Template
