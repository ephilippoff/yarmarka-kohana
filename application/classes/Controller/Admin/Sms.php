<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Sms extends Controller_Admin_Template {

	protected $module_name = 'sms';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Sms');
		
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
				'controller' => 'sms',
				'action'     => 'index',
			));							
	}


	public function action_emails()
	{
		$limit  = Arr::get($_GET, 'limit', 30);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Email');

		$search_filters = array();

		if ($title = $this->request->query('title'))
		{
			$search_filters["title"] = strtolower(trim($title));
			$list = $list->where(DB::expr('w_lower("title")'),'LIKE','%'.mb_strtolower(trim($title)).'%');
		} 

		if ($recipient = $this->request->query('recipient'))
		{
			$search_filters["recipient"] = strtolower(trim($recipient));
			$list = $list->where(DB::expr('w_lower("recipient")'),'LIKE','%'.mb_strtolower(trim($recipient)).'%');
		} 

		if ($date = $this->request->query('date'))
		{
			$search_filters["date"] = $date;
			if ( $date['from'] ) {
				$list = $list->where('created_on','>=',$date['from']);
			}
			if ( $date['to'] ) {
				$list = $list->where('created_on','<=',$date['to']);
			}
			
		} 
		
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
				'controller' => 'sms',
				'action'     => 'emails',
			));

		$this->template->search_filters = $search_filters;
	}

	public function action_email()
	{
		$this->layout = "shell";

		$id   = $this->request->param('id');
		
		$email = ORM::factory('Email', $id);

		if (!$email->loaded()) {
			throw new HTTP_Exception_404;
		}
		
		$this->template->email = $email;
		
	}
		
}
