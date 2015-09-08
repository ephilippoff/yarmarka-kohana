<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Seopatterns extends Controller_Admin_Template {

	protected $module_name = 'seopattern';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Seopattern')
				->with_categories();
		
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
				'controller' => 'seopatterns',
				'action'     => 'index',
			));							
	}
	
	public function action_add()
	{
		$this->template->errors = array();	
		
		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;																	
				
				ORM::factory('Seopattern')->values($post)->save();	
				
				$this->redirect('khbackend/seopatterns/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
		
		$this->template->categories = ORM::factory('Category')
				->where('is_ready', '=', 1)
				->order_by('title')
				->find_all();		
	}
	
	public function action_edit()
	{
		$this->template->errors = array();
		
		$item = ORM::factory('Seopattern', $this->request->param('id'));
		
		if ( ! $item->loaded())
		{
			throw new HTTP_Exception_404;
		}		
		
		if (HTTP_Request::POST === $this->request->method()) 
		{
			try
			{			
				$post = $_POST;
								
				$item->values($post)
				->save();

				$this->redirect('khbackend/seopatterns/index');
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->template->errors = $e->errors('validation');
			}
		}
		
		$this->template->categories = ORM::factory('Category')
				->where('is_ready', '=', 1)
				->order_by('title')
				->find_all();		

		$this->template->item = $item;
	}	
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$item = ORM::factory('Seopattern', $this->request->param('id'))->delete();
					
		$this->redirect('khbackend/seopatterns/index');

	}
		
}
