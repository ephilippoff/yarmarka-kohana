<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template {

	var $user; // current user
        private $errors = array();

	public function before()
	{
		parent::before();

		if ( ! $this->user = Auth::instance()->get_user())
		{
			if (Request::current()->action() != 'userpage')
			{
				$this->redirect(CI::site('user/login?return=user/'.$this->request->action()));
			}
		}
	}

	public function action_profile()
	{
		$this->layout = 'users';
		$this->assets->js('ajaxfileupload.js')
			->js('jquery.maskedinput-1.2.2.js')
			->js('jquery-ui/ui/minified/jquery.ui.core.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.widget.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.position.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.menu.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.autocomplete.min.js')
			->css('jquery-ui/themes/base/minified/jquery-ui.min.css')
			->js('profile.js');
			//->js('maps.js');

		$this->template->region_id	= $region_id = $this->user->user_city->loaded() 
			? $this->user->user_city->region_id 
			: Kohana::$config->load('common.default_region_id');
		$this->template->city_id	= $this->user->city_id;
		$this->template->regions	= ORM::factory('Region')
			->order_by('title')
			->find_all();
		$this->template->cities		= $region_id 
			? ORM::factory('City')
				->where('region_id', '=', $region_id)
				->order_by('title')
				->find_all()
			: array();

		$this->template->contact_types	= ORM::factory('Contact_Type')->find_all();
		$this->template->user_contacts	= $this->user->get_contacts();
		$this->template->user			= $this->user;
		$this->template->user_page_url  = substr(URL::base('http'), 0, strlen(URL::base('http')) - 1).URL::site('users/'.$this->user->login);
	}

	public function action_units()
	{
		$this->layout = 'users';
		$this->assets
                        ->js('ajaxfileupload.js')
			->js('jquery.maskedinput-1.2.2.js')
			->js('jquery-ui/ui/minified/jquery.ui.core.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.widget.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.position.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.menu.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.autocomplete.min.js')
			->css('jquery-ui/themes/base/minified/jquery-ui.min.css');
			//->js('chosen.jquery.js')
			//->js('profile.js');
		$this->template->region_id	= $region_id = $this->user->user_city->loaded() 
			? $this->user->user_city->region_id 
			: Kohana::$config->load('common.default_region_id');
			
		$this->template->units		= ORM::factory('Unit')
			->order_by('title')
			->find_all();
                
                /*foreach ($this->template->units as $unit) {
                    var_dump($unit->title);
                }*/
               
		$this->template->regions	= ORM::factory('Region')
			->order_by('title')
			->find_all();
		$this->template->cities		= $region_id 
			? ORM::factory('City')
				->where('region_id', '=', $region_id)
				->order_by('title')
				->find_all()
			: array();			

		$this->template->user_units		= $this->user->units->find_all();
		$this->template->user			= $this->user;
		$this->template->user_page_url  = substr(URL::base('http'), 0, strlen(URL::base('http')) - 1).URL::site('users/'.$this->user->login);
	}
        
    public function action_addunit()
    {
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;

        if (HTTP_Request::POST === $this->request->method())
        {
            try
            {
            	$location = Location::add_location_by_post_params();
                $user_unit = ORM::factory('User_Units')
                    ->set('user_id', $this->user->id)
                    ->set('unit_id', $_POST['unit_id'])
                    ->set('title', $_POST['title'])
                    ->set('web', $_POST['web'])
                    ->set('contacts', $_POST['contacts'])
                    ->set('description', $_POST['description'])
                    ->set('filename', $_POST['unit_image_filename'])
                    ->set('locations_id', $location->id)
                    ->save();

            }
            catch(ORM_Validation_Exception $e)
            {
                // collect errors
                $errors = $e->errors('validation');
                if (isset($errors['_external']))
                {
                    $errors += $errors['_external'];
                    unset($errors['_external']);
                }

                $this->errors = $errors;
            }
            catch (Exception $e) // file upload error
            {
                $this->errors['avatar'] = $e->getMessage();
            }
            
            $this->redirect('user/units');
        }
    }

    public function action_favorites()
	{
		$this->layout = 'users';
		$this->assets->js('favorites.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		$region	= ORM::factory('Region', intval($this->request->query('region_id')));
		$city	= ORM::factory('City', intval($this->request->query('city_id')));

		$favorites = ORM::factory('Object')->user_favorites($this->user->id)
			->with_main_photo();

		if ($region->loaded())
		{
			$favorites->where_region($region->id);
		}

		if ($city->loaded())
		{
			$favorites->where('city_id', '=', $city->id);
		}

		if ($text = trim($this->request->query('text')))
		{
			$favorites->where(DB::expr('w_lower(full_text)'), 'LIKE', '%'.mb_strtolower($text, 'UTF-8').'%');
		}

		$count = clone $favorites;
		$count = $count->count_all();

		$favorites->limit($per_page)
			->offset($per_page*($page-1))
			->order_by('date_created', 'desc');

		$this->template->regions = ORM::factory('Region')
			->where('is_visible', '=', 1)
			->find_all();
		$this->template->cities = $region->loaded() 
			? $region->cities->where('is_visible', '=', '1')->find_all()
			: array();
	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			'count_out'	=> 5,
			'count_in' => 5
		))->route_params(array(
			'controller' => 'user',
			'action' => 'favorites',
		));
		$this->template->objects = $favorites->find_all();
	}

	public function action_subscriptions()
	{
		$this->layout = 'users';
		$this->assets->js('subscriptions.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		$subscriptions = ORM::factory('Subscription')
			->where('user_id', '=', $this->user->id);

		$count = clone $subscriptions;
		$count = $count->count_all();

		$subscriptions->limit($per_page)
			->offset($per_page*($page-1));

		$this->template->subscriptions = $subscriptions->find_all();
	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			'count_out'	=> 5,
			'count_in' => 5
		))->route_params(array(
			'controller' => 'user',
			'action' => 'subscriptions',
		));
	}

	public function action_invoices()
	{
		$this->layout = 'users';
		$this->assets->js('invoices.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		$invoices = ORM::factory('Invoice')
			->where('user_id', '=', $this->user->id);

		// filter by state
		switch (Arr::get($_GET, 'status')) 
		{
			case 'created':
				$invoices->created();
			break;

			case 'success':
				$invoices->success();
			break;

			case 'refused':
				$invoices->refused();
			break;
			
			default:
			break;
		}

		$count = clone $invoices;
		$count = $count->count_all();

		$invoices->limit($per_page)
			->offset($per_page*($page-1))
			->order_by('created_on', 'desc');

	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			'count_out'	=> 5,
			'count_in' => 5
		))->route_params(array(
			'controller' => 'user',
			'action' => 'invoices',
		));

		$this->template->invoices = $invoices->find_all();
	}

	public function myads($folder = 'myads')
	{
		$this->template = View::factory('user/myads');
		$this->layout = 'users';
		$this->assets->js('myads.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		// get objects
		$objects = ORM::factory('Object')
			->with_main_photo()
			->where('active', '=', 1);

		switch ($folder) 
		{
			case 'published':
				$objects->where('author', '=', $this->user->id)
					->where('is_published', '=', '1')
					->where('is_bad', '=', '0');
			break;

			case 'unpublished':
				$objects->where('author', '=', $this->user->id)
					->where('is_published', '=', '0')
					->where('is_bad', '=', '0');
			break;

			case 'in_archive':
				$objects->where('author', '=', $this->user->id)
					->where('in_archive', '=', '1');
			break;

			case 'rejected':
				$objects->where('author', '=', $this->user->id)
					->where('is_bad', '=', 1);
			break;

			case 'banned':
				$objects->where('author', '=', $this->user->id)
					->where('is_bad', '=', 2);
			break;

			case 'from_employees':
				$this->template->linked_user = ORM::factory('User', $this->request->param('id'));
				$objects->where('author_company_id', '=', $this->user->id)
					->where('author', '=', $this->request->param('id'));
			break;

			default:
				$objects->where('author', '=', $this->user->id);
			break;
		}

		// region and city for filter
		$region		= ORM::factory('Region', intval($this->request->query('region_id')));
		$city		= ORM::factory('City', intval($this->request->query('city_id')));
		$category	= ORM::factory('Category', intval($this->request->query('category_id')));

		if ($region->loaded())
		{
			$objects->where_region($region->id);
		}

		if ($city->loaded())
		{
			$objects->where('city_id', '=', $city->id);
		}

		if ($category->loaded())
		{
			$objects->where('category', '=', $category->id);
		}

		// filter by text
		if ($text = trim($this->request->query('text')))
		{
			$objects->where(DB::expr('w_lower(full_text)'), 'LIKE', '%'.mb_strtolower($text, 'UTF-8').'%');
		}

		// count all user objects
		$count = clone $objects;
		$count = $count->count_all();

		// get user objects
		$objects = $objects->order_by('date_created', 'desc')
			->limit($per_page)
			->offset($per_page*($page-1))
			->find_all();				

		// get user objects categories
		$this->template->categories = ($objects_ids = $objects->as_array(NULL, 'id')) 
			? DB::select(DB::expr('COUNT(object.id)'))
			->select('category.id')
			->select('category.title')
			->from('object')
			->join('category')->on('object.category', '=', 'category.id')
			->where('object.id', 'IN', $objects_ids)
			->group_by('category.id')
			->group_by('category.title')
			->order_by('category.title')
			->as_object()
			->execute()
			: array();

	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			'count_out'	=> 5,
			'count_in' => 5
		))->route_params(array(
			'controller' => 'user',
			'action' => $folder,
		));
		$this->template->regions = ORM::factory('Region')
			->where('is_visible', '=', 1)
			->find_all();
		$this->template->cities = $region->loaded() 
			? $region->cities->where('is_visible', '=', '1')->find_all()
			: array();
		$this->template->objects = $objects;
	}

	public function action_myads()
	{
		$this->myads();
	}

	public function action_published()
	{
		$this->myads('published');
	}

	public function action_unpublished()
	{
		$this->myads('unpublished');
	}

	public function action_in_archive()
	{
		$this->myads('in_archive');
	}

	public function action_rejected()
	{
		$this->myads('rejected');
	}

	public function action_banned()
	{
		$this->myads('banned');
	}

	public function action_from_employees()
	{
		$this->myads('from_employees');
	}

	public function action_newspapers()
	{
		$this->layout = 'users';
		$this->assets->js('newspapers.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		$user_papers = ORM::factory('Service_Outputs')
			->where('user_id', '=', $this->user->id);

		$count = clone $user_papers;
		$count = $count->count_all();

		$user_papers->with('planningofnumber:edition')
			->limit($per_page)
			->offset($per_page*($page-1))
			->order_by('date_to_show', 'desc');

		$this->template->main_category = ORM::factory('Category', 1);
	 	$this->template->pagination = Pagination::factory( array(
			'current_page' => array('source' => 'query_string', 'key' => 'page'),
			'total_items' => $count,
			'items_per_page' => $per_page,
			'auto_hide' => TRUE,
			'view' => 'pagination/floating',
			'first_page_in_url' => TRUE,
			'count_out'	=> 5,
			'count_in' => 5
		))->route_params(array(
			'controller' => 'user',
			'action' => 'newspapers',
		));
		$this->template->user_papers = $user_papers->find_all();
	}

	public function action_office()
	{
		$this->layout = 'users';
		$this->assets->js('office.js');

		$this->template->users = $this->user->users->find_all();
		$this->template->links = ORM::factory('User_Link_Request')
			->where('user_id', '=', $this->user->id)
			->order_by('created', 'desc')
			->find_all();
	}

	public function action_affiliates()
	{
		$this->layout = 'users';
		$this->assets->js('affiliates.js')
			->js('jquery.maskedinput-1.2.2.js');

		$this->template->errors = array();

		if (HTTP_Request::POST === $this->request->method())
		{
			$_POST['fullname'] = trim($_POST['fullname']);
			$validation = Validation::factory($_POST)
				->rule('fullname', 'not_empty')
				->rule('org_type', 'not_empty')
				->rule('city_id', 'not_empty')
				->label('fullname', 'Название')
				->label('org_type', 'Тип')
				->label('city_id', 'Город');

			$user = ORM::factory('User')->values($_POST, array('fullname', 'org_type', 'city_id', 'address', 'url'));
			$user->parent_id 	= Auth::instance()->get_user()->id;
			$user->passw		= Text::random();
			$user->role 		= 2; // default role
			$user->code			= '';

			try
			{
				$user->filename 	= Uploads::save($_FILES['avatar']);
				Database::instance()->begin();

				$user->save($validation);

				if ($user_contacts = array_combine((array) $this->request->post('contact_type'), (array) $this->request->post('contact')))
				{
					foreach ($user_contacts as $type => $contact)
					{
						if (trim($contact))
						{
							$user->add_contact($type, $contact);
						}
					}
				}
				
				//Database::instance()->commit();
				Database::instance()->rollback();
			}
			catch(ORM_Validation_Exception $e)
			{
				// rollback transaction
				Database::instance()->rollback();

				// collect errors
				$errors = $e->errors('validation');
				if (isset($errors['_external']))
				{
					$errors += $errors['_external'];
					unset($errors['_external']);
				}

				$this->template->errors = $errors;
			}
			catch (Exception $e) // file upload error
			{
				$this->template->errors['avatar'] = $e->getMessage();
			}
		}

		$this->template->types = ORM::factory('User_Types')
			->where('parent_id', '=', 2)
			->find_all()
			->as_array('id', 'name');
		$this->template->regions = ORM::factory('Region')
			->where('is_visible', '=', 1)
			->order_by('title')
			->find_all()
			->as_array('id', 'title');
		$this->template->contact_types	= ORM::factory('Contact_Type')->find_all();
		$this->template->affiliates		= ORM::factory('User')
			->where('parent_id', '=', $this->user->id)
			->find_all();
	}

	public function action_userpage()
	{
		$this->layout = 'userpage';
		$this->assets->js('userpage.js')
			->js('ajaxfileupload.js')
			->js('maps.js');
		
		$user = ORM::factory('User')->where('login', '=', $this->request->param('login'))->find();

		if ( ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		$job_category_id = 36;//TODO: Костыль: Пропись id

		$this->template->job_adverts_count = $job_adverts_count = ORM::factory('Object')
				->where('author_company_id', '=', $user)
				->where('active', '=', 1)
				->where('is_published', '=', 1)
				->where('category', '=', $job_category_id)
				->where('date_expired', '<=',  DB::expr('CURRENT_TIMESTAMP'))
				->count_all();

		
		$this->template->is_owner = (Auth::instance()->get_user() AND Auth::instance()->get_user()->id === $user->id);
		$this->template->filter_href = ORM::factory('Category')->where('id', '=', 1)->find()->get_url().'?user_id='.$user->id;
		$this->template->job_category_href = ( $job_adverts_count > 0 )
				? 
				ORM::factory('Category')->where('id', '=', $job_category_id)->find()->get_url().'?user_id='.$user->id 
				: 
				'';
		$title = (empty($user->org_name)) ? "Страница компании №".$user->id : htmlspecialchars($user->org_name);		
		
		Seo::set_title($title);
		
		$this->template->user = $user;
	}

	public function action_upload_user_avatar()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array('code' => 200);

		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$user->filename = Uploads::save($_FILES['avatar_input']);
			$this->json['filename'] = Uploads::get_file_path($user->filename, '272x203');
			$user->save();
		}
		catch (Exception $e)
		{
			$this->json['error']	= $e->getMessage();
			$this->json['code']		= $e->getCode();
		}

		$this->response->body(json_encode($this->json));
	}
        
	public function action_upload_unit_image()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array('code' => 200);

		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$filename = Uploads::save($_FILES['unit_image_input']);
			$this->json['filename_to_save'] = $filename;
			$this->json['filename_big'] = Uploads::get_file_path($filename, '208x208');
			$this->json['filename'] = Uploads::get_file_path($filename, '136x136');
		}
		catch (Exception $e)
		{
			$this->json['error']	= $e->getMessage();
			$this->json['code']		= $e->getCode();
		}

		$this->response->body(json_encode($this->json));
	}

	public function action_upload_userpage_banner()
	{
		
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array('code' => 200);

		if ( ! $user = Auth::instance()->get_user() OR $user->org_type != 2)
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$filename = Uploads::save($_FILES['banner_input'], array('width' => 1202, 'height' => 1024));
			$user->userpage_banner = $this->json['filepath'] = Uploads::get_file_path($filename, '1280x292');
			$user->save();
		}
		catch (Exception $e)
		{
			$this->json['error']	= $e->getMessage();
			$this->json['code']		= $e->getCode();
		}

		$this->response->body(json_encode($this->json));
	}
	
	public function action_remove_unit() {
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array();
		
		$id = $_POST['id'];
		if(!empty($id)) {
			$unit = ORM::factory('User_Units')->where('id', '=', $id)->find();
			$unit->delete();
			$this->json['success'] = true;
		} else {
			$this->json['success'] = false;
		}
		$this->response->body(json_encode($this->json));
	}
	
	public function action_remove_image() {
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array();
		
		$id = $_POST['id'];
		if(!empty($id)) {
			$unit = ORM::factory('User_Units')->where('id', '=', $id)->find();
			$unit->set('filename', null);
			$unit->save();
			$this->json['success'] = true;
		} else {
			$this->json['success'] = false;
		}
		$this->response->body(json_encode($this->json));
	}
	
	public function action_edit_unit_image() {
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$this->json = array();
		
		$id = $_POST['id'];
		if(!empty($id)) {
			$unit = ORM::factory('User_Units')->where('id', '=', $id)->find();
			$unit->set('filename', $_POST['filename']);
			$unit->save();
			$this->json['success'] = true;
		} else {
			$this->json['success'] = false;
		}
		$this->response->body(json_encode($this->json));
	}

	public function action_logout()
	{
		if (Auth::instance()->get_user())
		{
			setcookie('user_id', '', time()-1, '/', Region::get_cookie_domain());
			Auth::instance()->logout();
		}

		$this->redirect('http://'.Region::get_current_domain().'/user/logout');
	}
}
/* End of file User.php */
/* Location: ./application/classes/Controller/User.php */