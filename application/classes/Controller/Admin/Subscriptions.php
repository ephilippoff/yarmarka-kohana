<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Subscriptions extends Controller_Admin_Template {

	protected $module_name = 'subscription';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Subscription');
		
		// количество общее
		$clone_to_count = clone $list;
		$count_all = $clone_to_count->count_all();		

		$list->limit($limit)->offset($offset); 		
		
		$this->template->list = $list->order_by('id', 'desc')->find_all();
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'subscriptions',
				'action'     => 'index',
			));							
	}
		
}
