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
			->order_by('title')
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
}
