<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template {

	var $user; // current user

	public function before()
	{
		parent::before();

		if ( ! $this->user = Auth::instance()->get_user())
		{
			/// @todo redirect to login form
			throw new HTTP_Exception_404;
		}
	}

	public function action_profile()
	{
		$this->layout = 'users';
		$this->assets->js('ajaxfileupload.js')
			->js('jquery.maskedinput-1.2.2.js')
			->js('profile.js');

		$this->template->region_id	= $region_id = $this->user->user_city->loaded() 
			? $this->user->user_city->region_id 
			: Kohana::$config->load('common.default_region_id');
		$this->template->city_id	= $this->user->city_id;
		$this->template->regions	= ORM::factory('Region')
			->order_by('title')
			->find_all();
		$this->template->cities		= $region_id 
			? ORM::factory('City')
				->where('region_id', '=', $region_id)
				->order_by('title')
				->find_all()
			: array();

		$this->template->contact_types	= ORM::factory('Contact_Type')->find_all();
		$this->template->user_contacts	= $this->user->get_contacts();
		$this->template->user			= $this->user;
	}

	public function action_favorites()
	{
		$this->layout = 'users';
		$this->assets->js('favorites.js');

		$region	= ORM::factory('Region', intval($this->request->query('region_id')));
		$city	= ORM::factory('City', intval($this->request->query('city_id')));

		$favorites = ORM::factory('Object')->user_favorites($this->user->id);

		if ($region->loaded())
		{
			$favorites->where_region($region->id);
		}

		if ($city->loaded())
		{
			$favorites->where('city_id', '=', $city->id);
		}

		if ($text = trim($this->request->query('text')))
		{
			$favorites->where(DB::expr('w_lower(full_text)'), 'LIKE', '%'.mb_strtolower($text, 'UTF-8').'%');
		}

		$favorites->limit(20);

		$this->template->regions = ORM::factory('Region')
			->where('is_visible', '=', 1)
			->find_all();
		$this->template->cities = $region->loaded() 
			? $region->cities->where('is_visible', '=', '1')->find_all()
			: array();
		$this->template->objects = $favorites->find_all();
	}

	public function action_subscriptions()
	{
		$this->layout = 'users';
		$this->assets->js('subscriptions.js');

		$per_page = 20;

		$this->template->subscriptions = ORM::factory('Subscription')
			->where('user_id', '=', $this->user->id)
			->limit($per_page)
			->find_all();
	}

	public function action_invoices()
	{
		$this->layout = 'users';
		$this->assets->js('invoices.js');

		$per_page = 20;

		$this->template->invoices = ORM::factory('Invoice')
			->where('user_id', '=', $this->user->id)
			->order_by('created_on', 'desc')
			->limit($per_page)
			->find_all();
	}

	public function myads($folder = 'myads')
	{
		$this->template = View::factory('user/myads');
		$this->layout = 'users';
		$this->assets->js('myads.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		// get objects
		$objects = ORM::factory('Object')
			->where('author', '=', $this->user->id);

		switch ($folder) 
		{
			case 'published':
				$objects->published();
			break;
			
			default:
				// all user objects
			break;
		}

		// region and city for filter
		$region	= ORM::factory('Region', intval($this->request->query('region_id')));
		$city	= ORM::factory('City', intval($this->request->query('city_id')));

		if ($region->loaded())
		{
			$objects->where_region($region->id);
		}

		if ($city->loaded())
		{
			$objects->where('city_id', '=', $city->id);
		}

		// filter by text
		if ($text = trim($this->request->query('text')))
		{
			$objects->where(DB::expr('w_lower(full_text)'), 'LIKE', '%'.mb_strtolower($text, 'UTF-8').'%');
		}

		// count all user objects
		$count = clone $objects;
		$count = $count->count_all();

		// get user objects
		$objects = $objects->order_by('date_created', 'desc')
			->limit($per_page)
			->offset($per_page*($page-1))
			->find_all();

		// get user objects categories
		$this->template->categories = DB::select(DB::expr('COUNT(object.id)'))
			->select('category.id')
			->select('category.title')
			->from('object')
			->join('category')->on('object.category', '=', 'category.id')
			->where('object.id', 'IN', $objects->as_array(NULL, 'id'))
			->group_by('category.id')
			->order_by('category.title')
			->as_object()
			->execute();

	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			//'uri_postfix' => '#reviews',
			'count_out'	=> 12,
			'count_in' => 10
		))->route_params(array(
			'controller' => 'user',
			'action' => $folder,
		));
		$this->template->regions = ORM::factory('Region')
			->where('is_visible', '=', 1)
			->find_all();
		$this->template->cities = $region->loaded() 
			? $region->cities->where('is_visible', '=', '1')->find_all()
			: array();
		$this->template->objects = $objects;
	}

	public function action_myads()
	{
		$this->myads();
	}

	public function action_published()
	{
		$this->myads('published');
	}

	public function action_affiliates()
	{
		// @todo
	}

	public function action_logout()
	{
		if (Auth::instance()->get_user())
		{
			setcookie('user_id', '', time()-1, '/', Region::get_cookie_domain());
			Auth::instance()->logout();
		}

		$this->redirect('http://'.Region::get_current_domain());
	}
} // End Welcome
