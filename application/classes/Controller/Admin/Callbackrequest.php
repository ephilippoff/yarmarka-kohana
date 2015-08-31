<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Callbackrequest extends Controller_Admin_Template {

	protected $module_name = 'callbackrequest';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;
		$s = trim(Arr::get($_GET, 's', ''));

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_created', 'status', 'id', 'key');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
		//Фильтр показа только активных, либо всех
			
		$requests = ORM::factory('Callbackrequest')->with_objects();
		if ($s) 
		{	
			$requests->where(DB::expr('lower(key)'), '=', mb_strtolower($s));	
		}		
		// количество общее
		$clone_to_count = clone $requests;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$requests->order_by($sort_by, $sort);		

		$requests->limit($limit)->offset($offset);
		
		$this->template->requests = $requests->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->s		  = $s;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'callbackrequest',
				'action'     => 'index',
			));		
	}
}	