<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ajax controller
 * 
 * @uses Controller
 * @uses _Template
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Controller_Ajax extends Controller_Template
{
	protected $json = array();

	public function before()
	{
		// only ajax request is allowed
		if ( ! $this->request->is_ajax())
		{
			throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		parent::before();

		$this->json['code'] = 200; // code by default
	}

	public function action_save_user_data()
	{
		$data = $this->request->post();
		$user = Auth::instance()->get_user();

		if ( ! $data OR ! $user)
		{
			throw new HTTP_Exception_404;
		}

		$user->values($data)->update();

		foreach ($data as $key => $value)
		{
			$data[$key] = $user->$key;
		}

		$this->json['data'] = $data;
	}

	public function after()
	{
		parent::after();
		$this->response->body(json_encode($this->json));
	}
}
