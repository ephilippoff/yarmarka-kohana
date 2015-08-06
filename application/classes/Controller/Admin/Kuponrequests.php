<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Kuponrequests extends Controller_Admin_Template {

	protected $module_name = 'kuponrequests';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_created', 'status', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
		//Фильтр показа только активных, либо всех
			
		$kupon_requests = ORM::factory('Object_Kupon_Requests')->with_objects();
		
		// количество общее
		$clone_to_count = clone $kupon_requests;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$kupon_requests->order_by($sort_by, $sort);		

		$kupon_requests->limit($limit)->offset($offset);
		
		// order
//		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
//		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';		

		$this->template->kupon_requests = $kupon_requests->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'kuponrequests',
				'action'     => 'index',
			));		
	}
}	