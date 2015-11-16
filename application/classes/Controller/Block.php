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
		// only sub requests is allowed
		if ($this->request->is_initial() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		parent::before();
	}

	public function action_header_region()
	{
		$region	= Region::get_current_region();
		$city	= Region::get_current_city();

		if ( ! $region AND ! $city)
		{
			$region = Region::get_default_region();
		}
		elseif ($city)
		{
			$region = $city->region;
		}

		$this->template->region = $region;
		$this->template->city	= $city;
		$this->template->cities	= $region->cities
			->where('seo_name', '!=', '')
			->order_by('sort_order')
			->limit(24)
			->cached(DATE::WEEK, array("city", "header"))
			->find_all();
	}

	public function action_header_left_menu()
	{
		$city_id = Region::get_current_city() ? Region::get_current_city()->id : 1;

		$categories = ORM::factory('Category')->get_categories_extend(array(
			"with_child" => TRUE, 
			"with_ads" => TRUE, 
			"city_id" => $city_id
		));
		
		//изымаем id всех родителей(для проверки наличия дочек)
		// foreach ($categories2l as $category) 
		// 	$parents_ids[] = $category->parent_id;
		
		$this->template->categories1l = $categories["main"];
		$this->template->categories2l = $categories["childs"];
		$this->template->parents_ids  = $categories["main_ids"];
		$this->template->banners	  = $categories["banners"];
	}

	public function action_header_search()
	{
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('title')
			->cached(DATE::WEEK, array("category", "header"))
			->find_all();
	}

	public function action_user_profile_contacts()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$user_contacts = ORM::factory('Contact')
			->select('contact_type.name')
			->with('contact_type')
			->where_user_id($user->id)
			->where('verified_user_id', 'IS', DB::expr('NULL'))
			->order_by('id')
			->find_all();

		$this->template->contact_types	= ORM::factory('Contact_Type')
			->where('id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::EMAIL))
			->find_all();
		$this->template->user_contacts	= $user_contacts;
	}

	public function action_verified_profile_contacts()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$user_contacts = ORM::factory('Contact')
			->select('contact_type.name')
			->with('contact_type')
			->where_user_id($user->id)
			->where('verified_user_id', '=', $user->id)
			->order_by('id')
			->find_all();

		$this->template->user 			= $user;
		$this->template->contact_types	= ORM::factory('Contact_Type')
			->where('id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::EMAIL))
			->find_all();
		$this->template->user_contacts	= $user_contacts;
	}

	public function action_user_contacts()
	{
		if ( ! $user = ORM::factory('User', $this->request->param('id')))
		{
			throw new HTTP_Exception_404;
		}

		$this->template->contact_types	= ORM::factory('Contact_Type')->find_all();
		$this->template->user_contacts	= $user->get_contacts();
	}

	public function action_last_moderator_comment()
	{
		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		$comments = $object->user_messages->from_moderator();

		$count = clone $comments;

		$this->template->count = $count->count_all();
		$this->template->comment = $comments->order_by('createdOn')
			->cached()
			->find();
	}

	public function action_error_404()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$domain = new Domain();
		if ($proper_domain = $domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}

		$twig = Twig::factory('errors/404');
		$twig->city = $domain->get_city();
		$twig->is_system_error = $this->request->post("is_system_error");

		$twig->categories = ORM::factory('Category')->get_categories_extend(array(
					"with_child" => TRUE, 
					"with_ads" => TRUE, 
					"city_id" => NULL
				));

		$this->response->body($twig);
	}

	public function action_articles_menu()
	{
		$this->template->top_parent = ORM::factory('Article', $this->request->param('id'))->get_top_parent();
	}

	public function action_articles_breadcrumbs()
	{
		$article = ORM::factory('Article', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$breadcrumbs = array();
		while ($article->loaded())
		{
			$breadcrumbs[] = array(
				'url' => Route::get('article')->uri(array('seo_name' => $article->seo_name)),
				'anchor' => $article->title,
			);
			
			$article = ORM::factory('Article', $article->parent_id);
		}

		$breadcrumbs[] = array(
			'url' => '/',
			'anchor' => 'Ярмарка',
		);

		$this->template->breadcrumbs = array_reverse($breadcrumbs);
	}
	
	public function action_newsone_breadcrumbs()
	{
		$article = ORM::factory('Article', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$breadcrumbs = array();
		while ($article->loaded())
		{
			$breadcrumbs[] = array(
				'url' => Route::get('newsone')->uri(array('id' => $article->id, 'seo_name' => $article->seo_name)),
				'anchor' => $article->title,
			);
			
			$article = ORM::factory('Article', $article->parent_id);
		}

		$breadcrumbs[] = array(
			'url' => '/',
			'anchor' => 'Ярмарка',
		);						

		$this->template->breadcrumbs = array_reverse($breadcrumbs);
	}
	
	public function action_ourservices_breadcrumbs()
	{
		$article = ORM::factory('Article', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$breadcrumbs = array();
		while ($article->loaded())
		{
			$breadcrumbs[] = array(
				'url' => Route::get('ourservices')->uri(array('seo_name' => $article->seo_name)),
				'anchor' => $article->title,
			);
			
			$article = ORM::factory('Article', $article->parent_id);
		}

		$breadcrumbs[] = array(
			'url' => '/',
			'anchor' => 'Ярмарка',
		);

		$this->template->breadcrumbs = array_reverse($breadcrumbs);
	}	
	
//	public function action_news_breadcrumbs()
//	{	
//		$this->template = View::factory('block/newsone_breadcrumbs');
//		
//		$breadcrumbs[] = array(
//			'url' => Route::get('news')->uri(),
//			'anchor' => 'Новости',
//		);		
//
//		$breadcrumbs[] = array(
//			'url' => '/',
//			'anchor' => 'Ярмарка',
//		);						
//
//		$this->template->breadcrumbs = array_reverse($breadcrumbs);
//	}	

	public function action_not_unique_contact_msg()
	{
		$this->template->not_unique_numbers = ORM::factory('Contact')
			->by_phone_number($this->request->param('number'))
			->where('verified_user_id', '!=', DB::expr('NULL'))
			->find_all();
	}

	public function action_user_link_requests()
	{
		$user = Auth::instance()->get_user();
		$links = array();
		if ($user)
		{
			$links = $user->link_requests->find_all();
		}

		$this->template->links = $links;
	}

	public function action_user_linked_to()
	{
		$this->template->user = Auth::instance()->get_user();
	}

	public function action_user_from_employees_menu()
	{
		$this->template->users = Auth::instance()->get_user()->users
			->find_all();
	}

	public function action_plan_info()
	{
//		$this->assets->js('http://yandex.st/underscore/1.6.0/underscore-min.js');
		$user = Auth::instance()->get_user();

		$user_plan = ORM::factory('User_Plan')->get_plans($user->id)->find_all();

		$this->template->user_plans = $user_plan;

		$not_yet_payment = Array();
		$category = ORM::factory('Category')->is_not_null("plan_name")->find_all();

		foreach ($category as $cat)
		{
			$check = Plan::get_plan_for_user_by_category($user->id, $cat->id);
			$count = (int) ORM::factory("Object")->get_active_by_user_and_category($user->id, $cat->id)->count_all();

			//if (!Plan::check_count($check, $count))
				$not_yet_payment[] = new Obj(Array(
										"title" 				=> $cat->title,
										"count" 				=> $count,
										"current_plan" 			=> $check->title,
										"current_plan_count" 	=> $check->count
									 ));
		}
		$this->template->not_yet_payment = $not_yet_payment;
		

		
	}

	public function action_massload_categories()
	{
		$user = Auth::instance()->get_user();
		$avail_categories = ORM::factory('User_Settings')->get_by_name($user->id, "massload")->find_all();
		
		$categories = Array();								
		foreach($avail_categories as $category)
		{
			try 
			{ 
				$cfg = Kohana::$config->load('massload/bycategory.'.$category->value);
				$categories[$category->value] = $cfg["name"];
			} catch(Exception $e){}
		}
		$this->template->categories = $categories;
	}
}

/* End of file Block.php */
/* Location: ./application/classes/Controller/Block.php */