<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Reklama extends Controller_Admin_Template {

	protected $module_name = 'reklama';

	public function action_index()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;	
		$s = trim(Arr::get($_GET, 's', ''));

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('start_date', 'end_date', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : '';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : '';		
		//Фильтр показа только активных, либо всех
		$only_active = isset($_GET['only_active']) ? 1 : 0;
			
		$reklama_list = ORM::factory('Reklama');
		//Фильтр по active = 1
		if ($only_active) $reklama_list->where('active', '=', 1);
		//Поиск
		if ($s) 
		{	
			$reklama_list->where_open()
							->where(DB::expr('lower(title)'), 'like', '%'.mb_strtolower($s).'%')
							->or_where(DB::expr('lower(comments)'), 'like', '%'.mb_strtolower($s).'%')
						->where_close();			
		}		
		// количество общее
		$clone_to_count = clone $reklama_list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$reklama_list->order_by($sort_by, $sort);		

		$reklama_list->limit($limit)->offset($offset);
		
		// order
//		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
//		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';		

		$this->template->ads_list = $reklama_list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->only_active = $only_active;
		$this->template->s = $s;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'reklama',
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

				if (isset($_FILES['image']))
				{
					$post['image'] = $this->_save_image($_FILES['image']);
				}
			
				if (isset($post['cities']))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}
				
				$post['active'] = isset($post['active']) ? 1 : 0;			
				
				//Указаны группы
				if (isset($post['reklama_group']))					
				{	
					//Группы
					$post['groups'] = '{'.join(',', $post['reklama_group']).'}';
					//По группам берем связанные с ними категории для записи в БД
					$in = '('.join(',', $post['reklama_group']).')';
					$categories = ORM::factory('Reklama_Group_Category')->where('group_id', 'in', DB::expr($in))->find_all()->as_array('id', 'category_id');
					//Оставляем уникальные id категорий
					$categories = array_unique($categories);
					//Если есть категории
					if (count($categories))
						$post['categories'] = '{'.join(',', $categories).'}';									
				}				
				
				ORM::factory('Reklama')->values($post)->save();				

				$this->redirect('khbackend/reklama/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
				
		$this->template->reklama_group = ORM::factory('Reklama_Group')->find_all()->as_array('id', 'name');
			
	}	
	
	public function action_edit()
	{	
		$this->template->errors = array();
		
		$ad_element = ORM::factory('Reklama', $this->request->param('id'));
		if ( ! $ad_element->loaded())
		{
			throw new HTTP_Exception_404;
		}			

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;			

				if (($_FILES['image']['name']))
				{			
					$post['image'] = $this->_save_image($_FILES['image']);
				}
				else
				{
					if (isset($post['delete_image']))
						$post['image'] = null;
				}
			
				if (isset($post['cities']))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}
				
				$post['active'] = isset($post['active']) ? 1 : 0;
				
				//Указаны группы
				if (isset($post['reklama_group']))
				{	
					$post['groups'] = '{'.join(',', $post['reklama_group']).'}';
					//По группам берем связанные с ними категории для записи в БД
					$in = '('.join(',', $post['reklama_group']).')';
					$categories = ORM::factory('Reklama_Group_Category')->where('group_id', 'in', DB::expr($in))->find_all()->as_array('id', 'category_id');
					//Оставляем уникальные id категорий
					$categories = array_unique($categories);
					//Если есть категории
					if (count($categories))
						$post['categories'] = '{'.join(',', $categories).'}';									
				}				
				
				$ad_element->values($post)->save();				

				$this->redirect('khbackend/reklama/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->ad_element = $ad_element;		
		$this->template->reklama_group = ORM::factory('Reklama_Group')->find_all()->as_array('id', 'name');
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$ads_element = ORM::factory('Reklama', $this->request->param('id'));

		if ( ! $ads_element->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (is_file(DOCROOT.'uploads/banners/'.$ads_element->image))
			unlink (DOCROOT.'uploads/banners/'.$ads_element->image);
		
		$ads_element->delete();
				
		$this->redirect('khbackend/reklama/index');

		//$this->response->body(json_encode(array('code' => 200)));
	}
	
	
	public function action_add_menu_banner()
	{
		$this->template->errors = array();

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;			

				if (isset($_FILES['image']))
				{
					$post['image'] = $this->_save_image($_FILES['image'], 'uploads/banners/menu/');
				}
			
				if (isset($post['cities']))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}
				
				ORM::factory('Category_Banners')->values($post)->save();
				
				//Сбрасываем кеш по ключам: state, city_id
				if ($_POST['state'] == 1)
					foreach ($_POST['cities'] as $city) 				
						Cache::instance()->set("getBannersForCategories:{$city}", NULL, 0);							

				$this->redirect('khbackend/reklama/menu_banners');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
				
		$this->template->categories = ORM::factory('Category')->where('parent_id', '=', 1)->find_all()->as_array('id', 'title');
			
	}
	
public function action_edit_menu_banner()
	{	
		$this->template->errors = array();
		
		$ad_element = ORM::factory('Category_Banners', $this->request->param('id'));
		if ( ! $ad_element->loaded())
		{
			throw new HTTP_Exception_404;
		}			

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;			

				if (($_FILES['image']['name']))
				{			
					//Удаляем старый баннер
					if (is_file(DOCROOT.'uploads/banners/menu/'.$ad_element->image))
						unlink (DOCROOT.'uploads/banners/menu/'.$ad_element->image);					
					
					$post['image'] = $this->_save_image($_FILES['image'], 'uploads/banners/menu/');					
				}
			
				if (isset($post['cities']))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}								
				
				$ad_element->values($post)->save();
				
				//Сбрасываем кеш по ключам: state, city_id
				foreach ($_POST['cities'] as $city) 				
					Cache::instance('memcache')->set("getBannersForCategories:{$city}", NULL, 0);	
					
				Cache::instance('memcache')->set("getBannerById:{$ad_element->id}", NULL, 0);

				$this->redirect('khbackend/reklama/menu_banners');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->ad_element = $ad_element;		
		$this->template->categories = ORM::factory('Category')->where('parent_id', '=', 1)->find_all()->as_array('id', 'title');
	}
	
	public function action_delete_menu_banner()
	{
		$this->auto_render = FALSE;

		$ads_element = ORM::factory('Category_Banners', $this->request->param('id'));

		if ( ! $ads_element->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (is_file(DOCROOT.'uploads/banners/menu/'.$ads_element->image))
			unlink (DOCROOT.'uploads/banners/menu/'.$ads_element->image);
		
		$ads_element->delete();
				
		$this->redirect('khbackend/reklama/menu_banners');

	}	
	
    protected function _save_image($image, $path = 'uploads/banners/')
    {
        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
        {
            return FALSE;
        }

        $directory = DOCROOT.$path;
 
        if ($file = Upload::save($image, NULL, $directory))
        {
            $filename = strtolower(Text::random('alnum', 20)).'.png';
 
            Image::factory($file)->save($directory.$filename);
 
            // Delete the temporary file
            unlink($file);
 
            return $filename;
        }
 
        return FALSE;
    }
	
	public function action_tickets()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_expiration', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : '';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : '';		
		//Фильтр показа только активных, либо всех
		$only_active = isset($_GET['only_active']) ? 1 : 0;
			
		$tickets_list = ORM::factory('Object_Service_Ticket');
		
		if ($only_active) $tickets_list->where('invoice_id', '>', 0);		
				
		// количество общее
		$clone_to_count = clone $tickets_list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$tickets_list->order_by($sort_by, $sort);		

		$tickets_list->order_by('id', 'DESC')->limit($limit)->offset($offset); 
		
		// order
//		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
//		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';		

		$this->template->tickets_list = $tickets_list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->only_active = $only_active;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'reklama',
				'action'     => 'tickets',
			));		
	}	
	
	
	public function action_menu_banners()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_expired', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : '';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : '';		
		//Фильтр показа только активных, либо всех
//		$only_active = isset($_GET['only_active']) ? 1 : 0;
			
		$banners_list = ORM::factory('Category_Banners');
		
//		if ($only_active) $tickets_list->where('invoice_id', '>', 0);		
				
		// количество общее
		$clone_to_count = clone $banners_list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$banners_list->order_by($sort_by, $sort);		

		$banners_list->limit($limit)->offset($offset); 
		
		// order
//		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
//		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';		

		$this->template->banners_list = $banners_list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
//		$this->template->only_active = $only_active;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'reklama',
				'action'     => 'menu_banners',
			));		
	}
	
	
	public function action_photocards()
	{
		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_expiration', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : '';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : '';		
		//Фильтр показа только активных, либо всех
		$only_active = isset($_GET['only_active']) ? 1 : 0;
			
		$photocards_list = ORM::factory('Object_Service_Photocard')
				->with_data()
				->where('object_service_photocard.type', '=', 1);

		if ($only_active) $photocards_list->where('invoice_id', '>', 0);
				
		// количество общее
		$clone_to_count = clone $photocards_list;
		$count_all = $clone_to_count->count_all();
		
		if ($sort_by and $sort)
			$photocards_list->order_by($sort_by, $sort);		

		$photocards_list->order_by('id', 'DESC')->limit($limit)->offset($offset); 
		
		// order
//		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
//		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';		

		$this->template->photocards_list = $photocards_list->find_all();
		$this->template->sort	  = $sort;
		$this->template->sort_by  = $sort_by;
		$this->template->only_active = $only_active;
		
		$this->template->limit	  = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'reklama',
				'action'     => 'photocards',
			));		
	}		

	
}

/* End of file Articles.php */
/* Location: ./application/classes/Controller/Admin/Articles.php */