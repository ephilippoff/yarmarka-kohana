<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Attributes extends Controller_Admin_Template {

	protected $module_name = 'attribute';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Attribute');
		
		// количество общее
		$clone_to_count = clone $list;
		$count_all = $clone_to_count->count_all();		

		$list->order_by('seo_name','asc')->limit($limit)->offset($offset); 		
		
		$this->template->list = $list->find_all();
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'attributes',
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
				
				ORM::factory('Attribute')->values($post)->save();	
				
				$this->redirect('khbackend/attributes/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}							
	}
	
public function action_edit()
	{
		$this->template->errors = array();

		$item = ORM::factory('Attribute', $this->request->param('id'));
		
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

				$this->redirect('khbackend/attributes/index');
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->item = $item;
	}	
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$item = ORM::factory('Attribute', $this->request->param('id'))->delete();
					
		$this->redirect('khbackend/attributes/index');

	}

	public function action_element_delete()
	{
		$this->auto_render = FALSE;

		$item = ORM::factory('Attribute_Element', $this->request->param('id'))->delete();
					
		$this->redirect('khbackend/attributes/element_index');

	}

	public function action_element_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		
		$list = ORM::factory('Attribute_Element');

		if ($attribute   = $this->request->query('attribute')) {
			$list = $list->where('attribute','=', (int) $attribute);
		}

		if ($parent_element   = $this->request->query('parent_element')) {
			$list = $list->where('parent_element','=',(int)  $parent_element);
		}
		
		// количество общее
		$clone_to_count = clone $list;
		$count_all = $clone_to_count->count_all();		

		if ($attribute OR $parent_element) {
			$list = $list->order_by('seo_name');
		} else {
			$list = $list->order_by('id','desc');
		}

		$list->limit($limit)->offset($offset); 		
		
		$this->template->list = $list->find_all();

		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'attributes',
				'action'     => 'index',
			));							

		$attribute_elements = array(0 => '---');

		$this->template->attributes =  ORM::factory('Attribute')->where('type','=','list')->order_by('id')->find_and_maptoid(function($item) use (&$attribute_elements){

			$elements = array();

			$elements = ORM::factory('Attribute_Element')->where('attribute','=',$item->id)->order_by('attribute')->cached(Date::WEEK)->order_by('seo_name')->find_and_maptoid(function($item){
				return sprintf('%s %s (%s)', $item->attribute, $item->title, $item->seo_name);
			});

			$name = sprintf('%s %s (%s)', $item->id, $item->title, $item->seo_name);

			if (count($elements)) {
				$attribute_elements[$name] = $elements;
			}

			return $name;
		});

		$this->template->attributes = $this->template->attributes;

		$this->template->parent_elements =  $attribute_elements;
	}

	public function action_element_add()
	{
		$this->template->errors = array();

		$item = ORM::factory('Attribute_Element');
		$id = $this->request->param('id');

		if ($id) {
			$item = ORM::factory('Attribute_Element', $id);
		}
		
		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;																	
				
				$post['parent_element'] = ($post['parent_element']) ? $post['parent_element'] : NULL;
				$item->values($post)->save();	
				
				$this->redirect('khbackend/attributes/element_index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}							

		$attribute_elements = array(0 => '---');

		$this->template->attributes = $attributes = ORM::factory('Attribute')->where('type','=','list')->order_by('id')->find_and_maptoid(function($item) use (&$attribute_elements){

			$elements = array();

			$elements = ORM::factory('Attribute_Element')->where('attribute','=',$item->id)->order_by('attribute')->cached(Date::WEEK)->order_by('seo_name')->find_and_maptoid(function($item){
				return sprintf('%s %s (%s)', $item->attribute, $item->title, $item->seo_name);
			});

			$name = sprintf('%s %s (%s)', $item->id, $item->title, $item->seo_name);

			if (count($elements)) {
				$attribute_elements[$name] = $elements;
			}

			return $name;
		});



		$this->template->parent_elements =  $attribute_elements;

		$this->template->item = (array) ((HTTP_Request::POST === $this->request->method()) ? $_POST : $item->get_row_as_obj());
	}

}
