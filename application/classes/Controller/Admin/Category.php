<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Category extends Controller_Admin_Template {

	protected $module_name = 'category';

	public function action_index()
	{
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('title')
			->find_all();
	}

	public function action_sub_categories()
	{
		$this->use_layout = false;
		
		$this->template->level 		= intval($this->request->query('level'))+1;
		$this->template->parent_id 	= $this->request->param('id');
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', $this->request->param('id'))
			->order_by('title')
			->find_all();
	}
	
	public function action_add()
	{
		$this->template->errors = array();
		
		if (HTTP_Request::POST === $this->request->method())
		{
			try 
			{				
				$post = $_POST;																	
				
				$category = ORM::factory('Category')->values($post)->save();
				
				$category->add('business_types', $this->request->post('business_types'));
				
				$this->redirect('khbackend/category/index');
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
		
		$this->template->business_types = ORM::factory('Business_Type')
			->find_all()
			->as_array('id', 'title');
	}	

	public function action_edit()
	{
		if ( ! $category = ORM::factory('Category', $this->request->param('id')))
		{
			throw new HTTP_Exception_404;
		}

		$this->template->errors = array();
		
		if (HTTP_Request::POST === $this->request->method())
		{
			try 
			{
				$post = $_POST;																	
				
				$category->values($post)
					->save()
					->remove('business_types')
					->add('business_types', $this->request->post('business_types'));

				$this->redirect('khbackend/category/index');
			}
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}		
		}
		
		$this->template->categories = ORM::factory('Category')
				->where('is_ready', '=', 1)
				->where('id', '<>', $category->id)
				->order_by('title')
				->find_all();		

		$selected = $category->business_types->find_all()->as_array(NULL, 'id');

		$this->template->category 		= $category;
		$this->template->business_types = ORM::factory('Business_Type')
			->find_all()
			->as_array('id', 'title');		
		$this->template->selected 		= $selected;
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$item = ORM::factory('Category', $this->request->param('id'))
				->remove('business_types')
				->delete();
					
		$this->redirect('khbackend/category/index');

	}	

	public function action_relations()
	{
		$this->template->categories = ORM::factory('Category')
			->order_by('id')
			->find_all();
	}

	public function action_relation_edit()
	{
		$this->use_layout = FALSE;
		$category_id =  $this->request->param('id');
		$this->template->category_id = $category_id;

		$references = Array();
		$references[""] = "-- Выбери атрибут --";
		$reference =  ORM::factory('Reference')
							->where('category', '=', $category_id)
							->order_by("weight")
							->find_all();
		foreach ($reference as $ref)
			$references[$ref->id] = $ref->attribute_obj->title;
		$this->template->references = $references;

		$relations = Array();
		$relations[""] = "-- Выбери родителя --";
		$relation =  ORM::factory('Attribute_Relation')
							->where('category_id', '=', $category_id)
							->find_all();
		foreach ($relation as $rel)
			$relations[$rel->id] = $rel->reference_obj->attribute_obj->title." (".$rel->id.")";
		$this->template->relations = $relations;
	}
	
	public function action_arelation_edit()
	{
		$this->use_layout = FALSE;
		$arel_id =  $this->request->param('id');
		
		$arel = ORM::factory('Attribute_Relation', $arel_id);
		
		$this->template->arel = $arel;

		$references = Array();
		
		$references[""] = "-- Выбери атрибут --";
		$reference =  ORM::factory('Reference')
							->where('category', '=', $arel->category_id)
							->order_by("weight")
							->find_all();
		
		foreach ($reference as $ref)
			$references[$ref->id] = $ref->attribute_obj->title;
		
		$this->template->references = $references;

		$relations = Array();
		$relations[""] = "-- Выбери родителя --";
		$relation =  ORM::factory('Attribute_Relation')
							->where('category_id', '=', $arel->category_id)
							->find_all();
		
		foreach ($relation as $rel)
			$relations[$rel->id] = $rel->reference_obj->attribute_obj->title." (".$rel->id.")";
		
		$this->template->relations = $relations;
	}

	public function action_parent_element()
	{
		$this->use_layout = FALSE;

		$relation_id =  $this->request->param('id');
		$relation =  ORM::factory('Attribute_Relation', $relation_id);

		$elements = array();
		$elements[""] = "-- Выбери роодительский элемент --";
		$aes = ORM::factory('Attribute_Element')
						->where("attribute","=",$relation->reference_obj->attribute)
						->find_all();
		foreach($aes as $ae)
			$elements[$ae->id] = $ae->title;

		$this->template->parent_element = Form::select("parent_element", $elements, NULL, 
											array('id' => 'parent_element'));
	}
	
	public function action_move_sort_relation()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		
		$relation_id =  (int)$this->request->post('id');
		$direction = (int)$this->request->post('direction');
		
		$relation =  ORM::factory('Attribute_Relation', $relation_id);
		
		//if ($relation->weight > 0) 
		{
			$relation->weight = $relation->weight + $direction;
			$relation->update();
		}

		$this->response->body(json_encode($relation->weight));
	}

	public function action_update_attribute()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$id =  (int)$this->request->param('id');
		echo Debug::vars($id);
		Task_Clear::set_attribute_element_seo_name($id);
		Task_Clear::set_attribute_element_urls($id);
		echo "OK";
	}

}

/* End of file Category.php */
/* Location: ./application/classes/Controller/Admin/Category.php */