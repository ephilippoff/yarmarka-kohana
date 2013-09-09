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
			->find_all();
	}

	public function action_header_left_menu()
	{
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('weight')
			->cached(60)
			->find_all();
	}

	public function action_header_search()
	{
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('title')
			->cached(60)
			->find_all();
	}

	public function action_user_profile_contacts()
	{
		if ( ! $user = Auth::instance()->get_user())
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
		$this->assets->css('css.css');
		$this->use_layout	= TRUE;
		$this->template		= View::factory('errors/404');

		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('weight')
			->cached(60)
			->find_all();
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

	public function action_not_unique_contact_msg()
	{
		$contact = ORM::factory('Object_Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->not_unique_numbers = $contact->get_not_unique_verified_numbers();
	}
}