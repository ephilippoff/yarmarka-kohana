<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template {

	public function action_profile()
	{
		$this->layout = 'users';
		$this->assets->js('ajaxfileupload.js')
			->js('profile.js');

		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->region_id	= $region_id = $user->user_city->loaded() 
			? $user->user_city->region_id 
			: Kohana::$config->load('common.default_region_id');
		$this->template->city_id	= $user->city_id;
		$this->template->regions	= ORM::factory('Region')
			->where('is_visible', '=', 1)
			->order_by('title')
			->find_all();
		$this->template->cities		= $region_id 
			? ORM::factory('City')
				->where('region_id', '=', $region_id)
				->where('is_visible', '=', 1)
				->order_by('title')
				->find_all()
			: array();

		$this->template->contact_types	= ORM::factory('Contact_Type')->find_all();
		$this->template->user_contacts	= $user->get_contacts();
		$this->template->user			= $user;

	}

} // End Welcome
