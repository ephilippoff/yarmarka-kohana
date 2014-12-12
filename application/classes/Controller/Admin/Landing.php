<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Landing extends Controller_Admin_Template {

	protected $module_name = 'landing';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$landing_list = ORM::factory('Landing');
		
		// количество общее
		$clone_to_count = clone $landing_list;
		$count_all = $clone_to_count->count_all();		

		$landing_list->limit($limit)->offset($offset); 		
		
		$this->template->landing_list = $landing_list->find_all();
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'landing',
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
				
				ORM::factory('Landing')->values($post)->save();	
				
				$name = $post['domain'].'.'.Kohana::$config->load("common.short_domain");
				
				$query = DB::insert('records', array('name'))->values(array("$name"))->execute('db_dns');

				$this->redirect('khbackend/landing/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}							
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$ads_element = ORM::factory('Landing', $this->request->param('id'));
		
		$name = trim($ads_element->domain).'.'.Kohana::$config->load("common.short_domain");

		if ( ! $ads_element->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		$ads_element->delete();
						
		$query = DB::delete('records')->where('name', '=', $name)->execute('db_dns');
				
		$this->redirect('khbackend/landing/index');

	}
		
}
