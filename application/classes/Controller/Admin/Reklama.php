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
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
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
	
	public function action_linkstat()
	{
		$cities = '';
		$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
		$date_start = Arr::get($_GET, 'date_start', date('Y-m-d', strtotime('- 7 days')));
		$date_end = Arr::get($_GET, 'date_end', date('Y-m-d'));
		
		$reklama_id = $this->request->param('id');
		$this->template->link = $link = ORM::factory('Reklama', $reklama_id);
		$this->template->stats = ORM::factory('Reklama_Linkstats')
				->where('reklama_id', '=', $reklama_id)
				->where('date', '>=', $date_start)
				->where('date', '<=', $date_end)
				->order_by('date')
				->find_all();
		
		foreach (explode(',', trim($link->cities,'{}')) as $code)
			if (isset($main_cities[$code])) 
				$cities .= $main_cities[$code].', ';
			
		$this->template->cities = $cities;
		$this->template->date_start = $date_start;
		$this->template->date_end = $date_end;		
	}
	
	public function action_menubannerstat()
	{
		$cities = '';
		$main_cities = array(1 => 'Тюменская область', 1919 => 'Тюмень', 1947 => 'Нефтеюганск', 1948 => 'Нижневартовск', 1979 => 'Сургут');
		$states = array(0 => 'Неактивна', 1 => 'Активна', 2 => 'Предпросмотр');
		$date_start = Arr::get($_GET, 'date_start', date('Y-m-d', strtotime('- 7 days')));
		$date_end = Arr::get($_GET, 'date_end', date('Y-m-d'));
		
		$banner_id = $this->request->param('id');
		$this->template->banner = $banner = ORM::factory('Category_Banners', $banner_id);
		$this->template->stats = ORM::factory('Category_Banners_Stats')
				->where('banner_id', '=', $banner_id)
				->where('date', '>=', $date_start)
				->where('date', '<=', $date_end)
				->order_by('date')
				->find_all();
		
		foreach (explode(',', trim($banner->cities,'{}')) as $code)
			if (isset($main_cities[$code])) 
				$cities .= $main_cities[$code].', ';
			
		$this->template->cities = $cities;
		$this->template->states = $states;
		$this->template->date_start = $date_start;
		$this->template->date_end = $date_end;		
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
				
				if ($post['menu_name'] == 'main')
					$post['category_id'] = $post['category_id'];
				elseif ($post['menu_name'] == 'kupon')
					$post['category_id'] = $post['kupon_category_id'];
				elseif ($post['menu_name'] == 'news')
					$post['category_id'] = $post['news_category_id'];
				
				
				ORM::factory('Category_Banners')->values($post)->save();
				
				//Сбрасываем кеш по ключам
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

		$_cities = ORM::factory('City')->where_open()
														->where('is_visible','=',1)
														->or_where('id','=',1)
														->where_close()
														->getprepared_all(array('id', 'title'));
		$cities = array();
		foreach ($_cities as $city) {
			$cities[$city->id] = $city->title;
		}

		$this->template->cities = $cities;
				
		$this->template->menu_names = array('main'  => 'Рубрики объявлений', 
											'kupon' => 'Рубрики купонов', 
											'news'  => 'Рубрики новостей');

		$this->template->categories = ORM::factory('Category')->where('parent_id', '=', 1)->find_all()->as_array('id', 'title');
		$this->template->kupon_categories = ORM::factory('Attribute_Element')
                        ->select('attribute_element.id',  'attribute_element.title', array('attribute.id', 'attribute_title') )
                        ->join("attribute")
                        	->on("attribute.id","=","attribute_element.attribute")
						->where('attribute', '=', 
								DB::select('id')
								->from('attribute')
								->where('seo_name', 'IN', array('category_1') )
								->order_by('title')
								->limit(1))
								->find_all()
						->as_array('id', 'title');

		$this->template->news_categories = ORM::factory('Attribute_Element')
                        ->select('attribute_element.id',  'attribute_element.title', array('attribute.id', 'attribute_title') )
                        ->join("attribute")
                        	->on("attribute.id","=","attribute_element.attribute")
						->where('attribute', '=', 
								DB::select('id')
								->from('attribute')
								->where('seo_name', 'IN', array('news-category') )
								->order_by('title')
								->limit(1))
								->find_all()
						->as_array('id', 'title');                        


/*        $result = array();
        $result[0] ="---";
        foreach ($this->template->kupon_categories as $item) {
        	$result[$item->id] =  $item->title."(".$item->attribute_title.")";
        }

        $this->template->kupon_categories = $result;
*/        
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
				
				if ($post['menu_name'] == 'main')
					$post['category_id'] = $post['category_id'];
				elseif ($post['menu_name'] == 'kupon')
					$post['category_id'] = $post['kupon_category_id'];
				elseif ($post['menu_name'] == 'news')
					$post['category_id'] = $post['news_category_id'];				
				
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
		
		$_cities = ORM::factory('City')->where_open()
														->where('is_visible','=',1)
														->or_where('id','=',1)
														->where_close()
														->getprepared_all(array('id', 'title'));
		$cities = array();
		foreach ($_cities as $city) {
			$cities[$city->id] = $city->title;
		}

		$this->template->cities = $cities;

		$this->template->categories = ORM::factory('Category')->where('parent_id', '=', 1)->find_all()->as_array('id', 'title');
		$this->template->menu_names = array('main' => 'Рубрики объявлений', 'kupon' => 'Рубрики купонов', 'news' => 'Рубрики новостей');
		$this->template->kupon_categories = ORM::factory('Attribute_Element')
                        ->select('id', 'title')
						->where('attribute', '=', 
								DB::select('id')
								->from('attribute')
								->where('seo_name', '=', 'category_1')
								->order_by('title')
								->limit(1))
                        ->find_all()
						->as_array('id', 'title');
		$this->template->news_categories = ORM::factory('Attribute_Element')
                        ->select('id', 'title')
						->where('attribute', '=', 
								DB::select('id')
								->from('attribute')
								->where('seo_name', '=', 'news-category')
								->order_by('title')
								->limit(1))
                        ->find_all()
						->as_array('id', 'title');		
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

		//prepare filters
		$filters = array(
				'groups' => array(
						'city' => array( 'label' => 'Город' ),
						'status' => array( 'label' => 'Статус' ),
						'category' => array( 'label' => 'Рубрика' )
					)
			);

		//get filters items

		//cities
		$filters['groups']['city']['items'] = array();
		$cities = ORM::factory('City')
			->visible(true)
			->order_by('title', 'asc')
			->find_all();
		foreach($cities as $city) {
			$filters['groups']['city']['items'][$city->id] = array(
					'id' => $city->id,
					'title' => $city->title
				);
		}

		//statuses
		$filters['groups']['status']['items'] = array(
				'1' => array( 'id' => 1, 'title' => 'Активные' )
			);

		//categories
		$filters['groups']['category']['items'] = array();
		$categories = ORM::factory('Category')
			->where('parent_id', '=', 1)
			->order_by('title', 'asc')
			->find_all();
		foreach($categories as $category) {
			$filters['groups']['category']['items'][$category->id] = array(
					'id' => $category->id,
					'title' => $category->title
				);
		}

		//check which filters is selected
		$filters['selected'] = array(
				'city' => null,
				'category' => null,
				'status' => null
			);
		foreach($_REQUEST as $key => $value) {
			if (!empty($value) && array_key_exists($key, $filters['selected']) && array_key_exists($value, $filters['groups'][$key]['items'])) {
				$filters['selected'][$key] = $value;
			}
		}

		//export filter data to template
		$this->template->filters = $filters;
		$this->template->filtersUrlPart = http_build_query($filters['selected']);

		//prepare filters done

		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;		

		//Возможные варианты сортировки
		$sorting_types = array('asc', 'desc');
		$sorting_fields   = array('date_expired', 'id');
		//Принимаем, сверяем параметры сортировки
		$sort	 = in_array($this->request->query('sort'), $sorting_types) ? $this->request->query('sort') : 'desc';
		$sort_by = in_array($this->request->query('sort_by'), $sorting_fields) ? $this->request->query('sort_by') : 'id';		
		//Фильтр показа только активных, либо всех
//		$only_active = isset($_GET['only_active']) ? 1 : 0;
			
		$banners_list = ORM::factory('Category_Banners');
		
//		if ($only_active) $tickets_list->where('invoice_id', '>', 0);	
		
		//push filters to query
		if ($filters['selected']['category']) {
			$banners_list->where('category_id', '=', $filters['selected']['category']);
		}
		if ($filters['selected']['city']) {
			$banners_list->where(DB::expr($filters['selected']['city']),'=',DB::expr('any(cities)'));
		}
		if ($filters['selected']['status']) {
			$banners_list->where('state', '=', 1);
		} 
				
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
	
	public function action_photocards_edit()
	{
		$ad_element = ORM::factory('Object_Service_Photocard', $this->request->param('id'));
		if ( ! $ad_element->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->categories = ORM::factory('Category')
			->order_by('title')
			->cached(Date::WEEK)
			->find_all()
			->as_array('id', 'title');

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;	
				
				$ad_element->values($post)->save();	

				$this->redirect('khbackend/reklama/photocards');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->ad_element = $ad_element;		

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


	public function action_statistic()
	{

		$this->template->cities =  array(
                                    1 => 'Все', 
                                    1919 => 'Тюмень', 
                                    1947 => 'Нефтеюганск', 
                                    1948 => 'Нижневартовск', 
                                    1979 => 'Сургут'
                                );

		$this->template->periods =  array(
                                    'day' => 'День',
                                    'week' => 'Неделя',
                                    'month' => 'Месяц',
                                    'year' => 'Год'
                                );

		$this->template->types =  array(
                                    'objects' => 'Новые объявления',
                                    'users' => 'Новые пользователи',
                                    'emails' => 'E-mail',
                                    'sms' => 'Sms',
                                    'orders' => 'Заказы',
                                    'subscriptions' => 'Подписки'
                                );

		$category_array = array();
		$category_list = ORM::factory('Category')
								->where("through_weight","IS NOT",NULL)
								->where("is_ready", "=", 1)
								->order_by("through_weight")
								->cached(DATE::WEEK, array("category", "add"))
								->find_all();
		foreach ($category_list as $item) {
			
			$childs = ORM::factory('Category')
				->where("parent_id","=",$item->id)
				->where("is_ready", "=", 1)
				->order_by("weight")
				->cached(DATE::WEEK, array("category", "add"))
				->find_all();
			if (count($childs)>0 AND $item->id <> 1)
			{
				$childs_array = array();
				foreach ($childs as $child) {
					if (!ORM::factory('Category')
							->where("parent_id","=",$child->id)
							->where("is_ready", "=", 1)
							->count_all(NULL, DATE::WEEK))
					{
						$childs_array[$child->id] = $child->title;
					}
				}
				$category_array[$item->title] = $childs_array;
			}
			
		}

		$category_array["Другие"] = array(
			42 => "Медицина, здоровье. Товары и услуги",
			156 => "В хорошие руки",
			72 => "Товары для детей"
		);

		// Необходимо переместить рубрику "Другие" над остальными

		$lastvalue = end($category_array);
		$lastkey = key($category_array);

		$tmp_array = array($lastkey=>$lastvalue);

		array_pop($category_array);

		$category_array = array_merge($tmp_array,$category_array);

		$this->template->category_list = $category_array;
	}

	public function action_statistic_data()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$type = $this->request->query('type');
		$period = $this->request->query('period');
		$from = $this->request->query('from');
		$city = $this->request->query('city');
		$category = $this->request->query('category');

		if ($city == 1) $city= NULL;

		if (!$period) $period = 'day';
 
		$json = array();

		$json['code'] = 200;
		$json['period'] = $period;


		switch ($type) {
			case 'objects':

					$filters = array(
						array('number','IS', NULL)
					);

					if ($from) {
						array_push($filters, array('real_date_created','>',$from) );
					} else {
						array_push($filters, array('real_date_created','>','2016-01-01') );
					}

					if ($city) {
						array_push($filters, array('city_id','=',$city) );
					}

					if ($category) {

						if (count($category) == 1) {
							$category = array($category);
						}

						array_push($filters, array('category','IN', $category) );

						$objects =  Statistic::get_new_objects_category( $period, $filters);

						$new_objects = array();
						foreach ($category as $category_id) {
							$new_object_category = array_filter($objects, function($item) use ($category_id){
								return $item['category'] == $category_id;	
							});
							$new_objects['new_objects_'.$category_id] = array_values($new_object_category);
						}

						$json['data'] = $new_objects;

					} else {
						$json['data'] = array(
							'new_objects' => Statistic::get_new_objects( $period, $filters)
						);
					}

					

				break;

			case 'users':

					$filters = array(
						array('is_blocked','=', 0)
					);

					if ($from) {
						array_push($filters, array('regdate','>',$from) );
					} else {
						array_push($filters, array('regdate','>','2016-01-01') );
					}

					$json['data'] = array(
						'new_users' => Statistic::get_new_user( $period, $filters )
					);

				break;

			case 'emails':

					$filters = array();

					if ($from) {
						array_push($filters, array('created_on','>',$from) );
					} else {
						array_push($filters, array('created_on','>','2016-01-01') );
					}

					$json['data'] = array(
						'emails' => Statistic::get_sent_emails( $period, $filters )
					);

				break;

			case 'sms':

					$filters = array();

					if ($from) {
						array_push($filters, array('created_on','>',$from) );
					} else {
						array_push($filters, array('created_on','>','2016-01-01') );
					}

					$json['data'] = array(
						'sms' => Statistic::get_sent_sms( $period, $filters )
					);

				break;

			case 'orders':

					$filters = array(
						array('state','=', 2)
					);

					if ($from) {
						array_push($filters, array('payment_date','>',$from) );
					} else {
						array_push($filters, array('payment_date','>','2016-01-01') );
					}

					$json['data'] = array(
						'orders' => Statistic::get_orders( $period, $filters )
					);

				break;

			case 'subscriptions':

					$filters = array(

					);

					if ($from) {
						array_push($filters, array('created','>',$from) );
					} else {
						array_push($filters, array('created','>','2016-01-01') );
					}

					$json['data'] = array(
						'subscriptions' => Statistic::get_subscriptions( $period, $filters )
					);

				break;

			default:
				$json['code'] = 404;
				break;
		}


		$this->response->body(json_encode($json));
	}

	
}

/* End of file Articles.php */
/* Location: ./application/classes/Controller/Admin/Articles.php */