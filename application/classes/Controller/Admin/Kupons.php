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
		
		$kupons->limit($limit)->offset($offset);
		
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
	
	public function action_groups()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		
		
		$kupon_objects = ORM::factory('Object')				
				->with_attr_value('balance', 'integer')
				->where('category', '=', 173);
		
		$clone_to_count = clone $kupon_objects;
		$count_all = $clone_to_count->count_all();		
		
		$kupon_objects = $kupon_objects
				->order_by('id', 'desc')
				->limit($limit)
				->offset($offset)				
				->find_all();		
		
		$kupon_objects_sum = ORM::factory('Kupon')->sum_by_field('count');
		$kupon_objects_sum = Dbhelper::convert_dbset_to_keyid_arr($kupon_objects_sum, 'object_id');
		
		$kupon_objects_ids = array_map(function($item){
			return $item->id;
		}, $kupon_objects->as_array());
		
		if ($kupon_objects_ids)
		{
			$kupon_details = ORM::factory('Kupon')->where('object_id', 'in', $kupon_objects_ids)->find_all();			
			$kupon_details = Dbhelper::dbset_to_groups_arr($kupon_details, 'object_id');		
		}
		
		$this->template->kupon_objects = $kupon_objects;
		$this->template->kupon_details = $kupon_details;
		$this->template->kupon_objects_sum = $kupon_objects_sum;
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'kupons',
				'action'     => 'groups',
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