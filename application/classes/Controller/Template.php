<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller_Template
 * 
 * @uses Controller
 * @abstract
 * @package 
 * @copyright 2011
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
abstract class Controller_Template extends Controller {
	/**
	 * @var  View  page template
	 */
	public $template	= 'template';

	/**
	 * Own layout file
	 * 
	 * @var string
	 * @access public
	 */
	public $layout		= 'default';

	/**
	 * Wrap view into layout or direct render
	 * 
	 * @var bool
	 * @access public
	 */
	public $use_layout	= TRUE;

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * assets of js and css files
	 * 
	 * @var object Assets::factory('all')
	 * @access public
	 */
	public $assets;

	public $body_class = '';

	protected function debug($var) {
		echo '<pre>'; var_dump($var); echo '</pre>';
	}

    public function before()
	{
		parent::before();

		// check user auth cookie
		// if ($hash = Arr::get($_COOKIE, 'user_id'))
		// {
		// 	list($user_id, $hash) = explode('_', $hash);
		// 	$user = ORM::factory('User', intval($user_id));
		// 	if ($user->loaded() AND $user->get_hash() === $hash)
		// 	{
		// 		if ( ! Auth::instance()->get_user() OR $user->id != Auth::instance()->get_user()->id)
		// 		{
		// 			Auth::instance()->force_login($user);
		// 		}
		// 	}
		// }
		
		// create assets object
		$this->assets = Assets::factory('all');

		// Get view filename
		$this->template = $this->request->controller().'/'.$this->request->action();
		if ($this->request->directory())
		{
			$this->template = $this->request->directory().'/'.$this->template;
		}

		$this->template = strtolower($this->template);

		if ($this->auto_render === TRUE)
		{
			if (Kohana::find_file('views', $this->template))
			{
				// Load the template
				$this->template = View::factory($this->template);
			}
		}
		
	}

	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			if ($this->use_layout === TRUE)
			{
				$layout = View::factory('layouts/'.$this->layout);
				$layout->_content	= $this->template->render();
				$layout->_assets		= $this->assets;
				$layout->_body_class	= $this->body_class;
				$this->response->body($layout->render());
			}
			else 
			{
				$this->response->body($this->template->render());
			}
		}

		parent::after();
	}

	public function getService($name) {
		return Services_Factory::instance($name);
	}

} // End template
