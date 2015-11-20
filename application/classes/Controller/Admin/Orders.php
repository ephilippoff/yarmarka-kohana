<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Orders extends Controller_Admin_Template {

	protected $module_name = 'orders';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		$id = Arr::get($_GET, 'id', '');

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
			
		$list = ORM::factory('Order');

		//Поиск
		if ($id) 
		{	
			$list->where('id', '=', (int)$id);			
		}		
		// количество общее
		$clone_to_count = clone $list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$list->order_by($sort_by, $sort);		

		$list->limit($limit)->offset($offset);
		
		// order
		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'id';

		$this->template->list = $list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->id = $id;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'orders',
				'action'     => 'index',
			));				
	}		
}
