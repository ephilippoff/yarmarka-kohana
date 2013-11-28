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
		$this->template->level 		= intval($this->request->query('level'))+1;
		$this->template->parent_id 	= $this->request->param('id');
		$this->template->categories = ORM::factory('Category')
			->where('parent_id', '=', $this->request->param('id'))
			->order_by('title')
			->find_all();
	}

	public function action_edit()
	{
		if ( ! $category = ORM::factory('Category', $this->request->param('id')))
		{
			throw new HTTP_Exception_404;
		}

		if (HTTP_Request::POST === $this->request->method())
		{
			// @todo catch validation errors if would be more than title field to save
			$category->title = $this->request->post('title');
			$category->save();

			$category->remove('business_types');
			$category->add('business_types', $this->request->post('business_types'));

			$this->redirect('khbackend/category');
		}

		$selected = $category->business_types->find_all()->as_array(NULL, 'id');

		$this->template->category 		= $category;
		$this->template->business_types = ORM::factory('Business_Type')
			->find_all()
			->as_array('id', 'title');
		$this->template->selected 		= $selected;
	}
}

/* End of file Category.php */
/* Location: ./application/classes/Controller/Admin/Category.php */