<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Filesstorage extends Controller_Admin_Template {
	
	protected $module_name = 'filesstorage';	

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 30);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_created', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
			
		$files_list = ORM::factory('Filesstorage');
		
		// количество общее
		$clone_to_count = clone $files_list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$files_list->order_by($sort_by, $sort);		

		$files_list->limit($limit)->offset($offset);			

		$this->template->files_list = $files_list->find_all();
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
				'controller' => 'filesstorage',
				'action'     => 'index',
			));		

		$this->template->errors = Session::instance()->get('errors');
		Session::instance()->delete('errors');
	}
	
	public function action_add()
	{		
		$this->template = View::factory('admin/filesstorage/index');	
		$errors = 0;
		
		if (HTTP_Request::POST === $this->request->method()) 
		{			
			$post = $_POST;			

			if (isset($_FILES['filename']))
			{
				$post['filename'] = $this->_save_file($_FILES['filename'], kohana::$config->load('filesstorage.path'));
			}												

			if ($post['filename'])
			{
				ORM::factory('Filesstorage')->values($post)->save();
			}
			else 
			{
				$errors = 1;
			}

			Session::instance()->set('errors', $errors);
			$this->redirect('khbackend/filesstorage/index');
		}							
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$file_row = ORM::factory('Filesstorage', $this->request->param('id'));

		if ( ! $file_row->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		list($filename, $ext) = explode('.', $file_row->filename);

		if (is_file(DOCROOT.kohana::$config->load('filesstorage.path').$file_row->filename))
			unlink (DOCROOT.kohana::$config->load('filesstorage.path').$file_row->filename);
		
		if (is_file(DOCROOT.kohana::$config->load('filesstorage.path').$filename.'_100x75.'.$ext))
			unlink (DOCROOT.kohana::$config->load('filesstorage.path').$filename.'_100x75.'.$ext);		
		
		$file_row->delete();
				
		$this->redirect('khbackend/filesstorage/index');
	}	
	
    protected function _save_file($file, $path = 'uploads/files/')
    {
        if (
            ! Upload::valid($file) 
			OR ! Upload::not_empty($file) 
			OR ! Upload::type($file, array_merge(kohana::$config->load('filesstorage.extensions.images'), kohana::$config->load('filesstorage.extensions.docs'))) 	
			)
        {
            return FALSE;
        }			
		
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));		
		$filename = md5(time());
		$filename_new = $filename.'.'.$ext;

        $directory = DOCROOT.$path;
 
        if ($file_save = Upload::save($file, $filename_new, $directory))
        {             
            if (in_array($ext, kohana::$config->load('filesstorage.extensions.images')))
				Image::factory($file_save)->resize(100, 75, Image::AUTO)->save($directory.$filename.'_100x75.'.$ext);

            return $filename_new;
        }
 		
        return FALSE;
    }	
	
	
}