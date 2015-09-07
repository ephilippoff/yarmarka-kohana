<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Kupons extends Controller_Admin_Template {
	
	protected $module_name = 'kupon';
	
	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		
		
		$kupons = ORM::factory('Kupon')
				->with_objects();
		
		$clone_to_count = clone $kupons;
		$count_all = $clone_to_count->count_all();		
		
		$this->template->kupons = $kupons->find_all();
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'kupons',
				'action'     => 'index',
			));		
	}
}