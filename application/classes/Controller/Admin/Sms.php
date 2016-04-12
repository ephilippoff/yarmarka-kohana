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
