<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Kupons extends Controller_Admin_Template {
	
	protected $module_name = 'kupon';
	
	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		
		$s = trim(Arr::get($_GET, 's', ''));
		
		$kupons = ORM::factory('Kupon')
				->with_objects();
		
		//Поиск
		if ($s) 
		{	
			$kupons->where_open()
				->where(DB::expr('lower(kupon.text)'), 'like', '%'.mb_strtolower($s).'%')
				->or_where(DB::expr('lower(kupon.number)'), 'like', '%'.mb_strtolower($s).'%')
			->where_close();			
		}		
		
		$clone_to_count = clone $kupons;
		$count_all = $clone_to_count->count_all();		
		
		$this->template->kupons = $kupons->order_by('id', 'desc')->find_all();
		$this->template->limit	  = $limit;
		$this->template->s		  = $s;
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
	
	public function action_edit()
	{	
		$this->template->errors = array();
		
		$ad_element = ORM::factory('Kupon', $this->request->param('id'));
		if ( ! $ad_element->loaded())
		{
			throw new HTTP_Exception_404;
		}			

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;									
				
				$ad_element->values($post)->save();				

				$this->redirect('khbackend/kupons/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->ad_element = $ad_element;		
	}
	
}