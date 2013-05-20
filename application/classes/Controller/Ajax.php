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
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
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

		// about field xss validation
		if (isset($data['about']))
		{
			$data['about'] = preg_replace("/&#?[a-z0-9]+;/i","", strip_tags($data['about'],'<em><strong><ol><ul><li><span><br><p>'));
		}

		// update userdata
		$user->values($data)
			->update();

		// return values back to client
		foreach ($data as $key => $value)
		{
			$data[$key] = $user->$key;
		}

		$this->json['data'] = $data;
	}

	public function action_upload_user_avatar()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$user->filename = Uploads::save($_FILES['avatar_input']);
			$this->json['filename'] = Uploads::get_file_path($user->filename, '125x83');
			$user->save();
		}
		catch (Exception $e)
		{
			$this->json['error']	= $e->getMessage();
			$this->json['code']		= $e->getCode();
		}
	}

	public function action_delete_user_avatar()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		if ($user->filename)
		{
			Uploads::delete($user->filename);
			$user->filename = NULL;
			$user->save();
		}
	}

	public function action_get_cities_by_region_id()
	{
		if ( ! $region = ORM::factory('Region', $this->request->post('region_id')))
		{
			throw new HTTP_Exception_404;
		}

		$this->json['cities'] = $region->cities
			->where('is_visible', '=', 1)
			->order_by('title')
			->find_all()
			->as_array('id', 'title');
	}

	public function action_delete_user_contact()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$user->delete_contact($this->request->post('contact_id'));
	}

	public function action_add_user_contact()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$contact_type_id	= intval($this->request->post('contact_type_id'));
		$contact			= trim($this->request->post('contact'));

		if ($contact AND $contact_type_id)
		{
			$contact = $user->add_contact($contact_type_id, $contact);
		}
		else
		{
			$this->json['code'] = 400;
		}
	}

	public function action_user_profile_contacts()
	{
		$this->response->body(Request::factory('block/user_profile_contacts')->execute());
	}

	public function after()
	{
		parent::after();
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}
}
