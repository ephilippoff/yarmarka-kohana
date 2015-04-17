<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template {

	var $user; // current user
        private $errors = array();

	public function before()
	{
		parent::before();

		if ( ! $this->user = Auth::instance()->get_user())
		{
			if (!in_array(Request::current()->action(), 
					array('userpage','registration','account_verification','login','logout','forgot_password','forgot_password_link','message')))
			{
				$this->redirect(Url::site('user/login?return='.$this->request->uri()));
			}
		} else {
			$this->user->reload();
			if ($this->user->is_blocked == 1 AND !in_array(Request::current()->action(), 
					array('userpage','login','logout','forgot_password','forgot_password_link','message')))
			{
				$this->redirect(Url::site('user/message?message=userblock'));
			}
		}

		if ($this->user AND !$this->user->is_valid_orginfo()
					AND in_array(Request::current()->action(), array('edit_ad','objectload','priceload','published')))
				{
					if ($this->user->is_expired_date_validation())
						HTTP::redirect("/user/orginfo?from=another");
				}
	}



	public function action_profile()
	{
		$this->redirect('/user/userinfo');
		$this->layout = 'users';
		$this->assets->js('ajaxfileupload.js')
			->js('jquery.maskedinput-1.2.2.js')
			->js('jquery-ui/ui/minified/jquery.ui.core.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.widget.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.position.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.menu.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.autocomplete.min.js')
			->js('require.js')
			->js('require.config.js')
			->css('jquery-ui/themes/base/minified/jquery-ui.min.css')
			->js('profile.js')
//			->js('http://yandex.st/underscore/1.5.2/underscore.js?v=1.0.101')
				;
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

		// $this->assets->js('info-tooltip.js');

		$this->assets
            ->js('ajaxfileupload.js')
			->js('jquery.maskedinput-1.2.2.js')
			->js('jquery-ui/ui/minified/jquery.ui.core.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.widget.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.position.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.menu.min.js')
			->js('jquery-ui/ui/minified/jquery.ui.autocomplete.min.js')
//			->js('jquery.tooltipster.min.js')
//			->css('tooltipster.css')
			->css('jquery-ui/themes/base/minified/jquery-ui.min.css');
			//->js('chosen.jquery.js')
			//->js('profile.js');
		$this->template->region_id	= $region_id = $this->user->user_city->loaded() 
			? $this->user->user_city->region_id 
			: Kohana::$config->load('common.default_region_id');
			
		$this->template->units		= ORM::factory('Unit')
			->order_by('title')
			->find_all();
		
		$this->template->business_types = ORM::factory('Business_Type')->find_all();
                
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
				$lat 				= $this->request->post('lat');
				$lon 				= $this->request->post('lon');
				$city_kladr_id 		= $this->request->post('city_kladr_id');
				$address_kladr_id 	= $this->request->post('address_kladr_id');
				$address 			= $this->request->post('address');

				$location = Kladr::save_address($lat, $lon, $address, $city_kladr_id, $address_kladr_id);            	

                $user_unit = ORM::factory('User_Units')
                    ->set('user_id', $this->user->id)
                    ->set('unit_id', $_POST['unit_id'])
                    ->set('title', strip_tags($_POST['title']))
                    ->set('web', strip_tags($_POST['web']))
                    ->set('contacts', strip_tags($_POST['contacts']))
                    ->set('description', strip_tags($_POST['description']))
                    ->set('filename', $_POST['unit_image_filename'])
                    ->set('locations_id', $location->id)
					->set('business_type_id', $_POST['business_type_id'])
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

    public function action_objectload()
    {
    	$this->layout = 'users';    	
//    	$this->assets->js('http://yandex.st/underscore/1.6.0/underscore-min.js');
    	$this->assets->js('http://yandex.st/backbone/1.1.2/backbone-min.js');  	
    	$this->assets->js('ajaxupload.js');

    	$already_agree = FALSE;
    	$us = ORM::factory('User_Settings')
    					->get_by_name($this->user->id, "massload_agreed")
						->find();
		$already_agree = $us->loaded();

		$hidehelp = FALSE;
    	$us = ORM::factory('User_Settings')
    					->get_by_name($this->user->id, "massload_hidehelp")
						->find();
		$hidehelp = !$us->loaded();

    	if (HTTP_Request::POST === $this->request->method())
		{
			$post = new Obj($_POST);
			if ($post->i_agree AND !$already_agree)
			{
				$us->user_id = $this->user->id;
				$us->name 	 = "massload_agreed";
				$us->value   = 1;
				$us->save();				
			}

			if ($post->hidehelp AND $hidehelp)
			{
				$us->user_id = $this->user->id;
				$us->name 	 = "massload_hidehelp";
				$us->value   = 1;
				$us->save();				
			}

			if (!$post->hidehelp AND !$hidehelp)
				$us->delete();				

			header("Refresh:0");
			exit;
		}

		$this->template->already_agree  = $already_agree;
		$this->template->hidehelp 		= $hidehelp;

    	$avail_categories = Kohana::$config->load('massload.frontend_load_category');
    	
    	$categories = Array();								
    	$categories_templatelink = Array();
    	foreach($avail_categories as $category)
    	{
    			$cfg = Kohana::$config->load('massload/bycategory.'.$category);
    			$categories[$category] = $cfg["name"];

    			$us = ORM::factory('User_Settings')
    					->get_by_name(13, $category)
    					->cached()
						->find();

				$categories_templatelink[$category] = ($us->value) ? $us->value : "#";
    	}
    	$this->template->categories = $categories;
    	$this->template->categories_templates = $categories_templatelink;
    	$this->template->config = Kohana::$config->load('massload/bycategory');
    	$this->template->free_limit = Kohana::$config->load('massload.free_limit');

    	$objectload 		= ORM::factory('Objectload');
    	$objectload_files   = ORM::factory('Objectload_Files');    	

		$oloads = $objectload->where("user_id","=",$this->user->id)
								->order_by("created_on", "desc")
								->limit(5)
								->find_all();		

		$this->template->objectloads = $objectload->get_objectload_list($oloads);
		$this->template->states 	 = $objectload->get_states();

    }

    public function action_objectload_file_list()
	{
		$this->layout = 'admin_popup';
		
		if ($this->user->role == 1 OR $this->user->role == 9)
			$of = ORM::factory('Objectload_Files', $this->request->param('id'));
		else
			$of = ORM::factory('Objectload_Files')
						->join("objectload")
							->on("objectload_files.objectload_id","=","objectload.id")
						->where("user_id","=",$this->user->id)
						->where("objectload_files.id","=",$this->request->param('id'))
						->find();
						
		if (!$of->loaded())
			throw new HTTP_Exception_404;

		$this->template->config = Kohana::$config->load('massload/bycategory.'.$of->category);
		$temp =  DB::select()->from("_temp_".$of->table_name);

		if ($this->request->query('errors'))
			$temp = $temp->where("error","=",1);

		$this->template->fields = array_keys(ORM_Temp::factory($of->table_name)->list_columns());

		$this->template->items =  $temp->order_by("id","asc")->as_object()->execute();
		
		$service_fields = Objectload::getServiceFields();
		unset($service_fields["object_id"]);
		unset($service_fields["text_error"]);
		$this->template->service_fields = array_keys( $service_fields );

		$this->template->statistic = $of->get_statistic();
	}

	public function action_priceload()
	{
		$this->layout = 'users';    	
//    	$this->assets->js('http://yandex.st/underscore/1.6.0/underscore-min.js');
    	$this->assets->js('http://yandex.st/backbone/1.1.2/backbone-min.js');  	
    	$this->assets->js('ajaxupload.js');

    	$already_agree = FALSE;
    	$us = ORM::factory('User_Settings')
    					->get_by_name($this->user->id, "priceload_agreed")
						->find();
		$already_agree = $us->loaded();

		$hidehelp = FALSE;
    	$us = ORM::factory('User_Settings')
    					->get_by_name($this->user->id, "priceload_hidehelp")
						->find();
		$hidehelp = !$us->loaded();

		$this->template->already_agree  = $already_agree;
		$this->template->hidehelp 		= $hidehelp;


		$priceloads = ORM::factory('Priceload');

		$pload = $priceloads->where("user_id","=",$this->user->id)
					->order_by("created_on","desc")
					->find_all();

		$this->template->priceloads = $pload;
		$this->template->states 	= $priceloads->get_states();


		$this->template->free_limit = Kohana::$config->load('priceload.free_limit');
	}

	public function action_pricelist()
	{
		$this->layout = 'admin_popup';
		
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = new Obj($this->request->post());
		$array_post_data = $this->request->post();

		$show_form = FALSE;
		if ($this->user->role == 1 OR $this->user->role == 9)
		{
			$pl = ORM::factory('Priceload', $this->request->param('id'));
			$show_form = TRUE;
		}
		else
			$pl = ORM::factory('Priceload')
						->where("user_id","=",$this->user->id)
						->where("id","=",$this->request->param('id'))
						->find();
						
		if (!$pl->loaded())
			throw new HTTP_Exception_404;

		$temp =  DB::select()->from("_temp_".$pl->table_name);

		if ($this->request->query('errors'))
			$temp = $temp->where("error","=",1);

		$service_fields = Priceload::getServiceFields();
		$fields = array_keys(ORM_Temp::factory($pl->table_name)->list_columns());
		$states = $pl->get_states();

		$this->template->fields = array_diff($fields, array_keys($service_fields), array("id"));
		$this->template->count =  $temp->execute()->count();	
		$this->template->items =  $temp->order_by("id","desc")->limit(50)->as_object()->execute();		
		$this->template->price_id = $this->request->param('id');
		$this->template->title = $pl->title;
		$this->template->state = $states[$pl->state];
		$this->template->service_fields = array_keys( $service_fields );
		$this->template->type_fields = Priceload::getTypeFields();

		if ($is_post)
		{
			$array_post_data["columns"] = implode(",", $fields);
			$pl->config = serialize($array_post_data);
			$pl->description = $array_post_data["description"];
			$pl->keywords = $array_post_data["keywords"];
			$pl->title = $array_post_data["title"];
			$pl->save();

			Priceload::resetObjectCache($this->request->param('id'));
		}

		$fsetting = new Obj();
		if ($pl->config)
		{
			$post_data = new Obj(unserialize($pl->config));
			$post_data->title = $pl->title;
			$post_data->description = $pl->description;
			$post_data->keywords = $pl->keywords;
			$fsetting = $post_data;
		}

		$this->template->show_form = $show_form;
		$this->template->fsetting = $fsetting;
		$this->template->data = $post_data;
	}

	public function action_objectunload()
    {
    	$this->layout = 'users';  

    	$avail_categories = Kohana::$config->load('massload.frontend_load_category');

    	$categories = Array();								
    	foreach($avail_categories as $category)
    	{
			$cfg = Kohana::$config->load('massload/bycategory.'.$category);
			$categories[$category] = $cfg["name"];
		}
		$this->template->categories = $categories;
		$this->template->free_limit = Kohana::$config->load('massload.free_limit');

		$already_agree = FALSE;
    	$us = ORM::factory('User_Settings')
    					->get_by_name($this->user->id, "massload_agreed")
						->find();
		$already_agree = $us->loaded();

		$this->template->already_agree = $already_agree;
    }

    public function action_objectunload_file()
    {
    	$this->autorender = FALSE;  

    	$category = $this->request->param('id');
    	$user_id = $this->user->id;

    	$limit = NULL;
    	$setting_limit = ORM::factory('User_Settings')
    							->get_by_name($user_id, "massload_limit")
    							->find();
    	if ($setting_limit->loaded())
    		$limit = (int) $setting_limit->value;

    	$objectunload = new Objectunload($user_id, $category);   
    	$objects = $objectunload->get_objects($limit);


    	$data = array();
    	$data[] = array();
    	$data[] = $objectunload->get_header();
    	foreach ($objects as $object) {
    		$data[] = $objectunload->row($object);
    	}
    	$objectunload->get_excel_file($data);

    }

    public function action_massload()
    {
    	$this->layout = 'users';    	
//    	$this->assets->js('http://yandex.st/underscore/1.6.0/underscore-min.js');
    	$this->assets->js('http://yandex.st/backbone/1.1.2/backbone-min.js');
    	$this->assets->js('ajaxupload.js');
    	$this->assets->js('massload.js');

    	
    	$avail_categories = ORM::factory('User_Settings')->get_by_name($this->user->id, "massload")->find_all();
    	
    	$categories = Array();								
    	foreach($avail_categories as $category)
    	{
    		try 
    		{ 
    			$cfg = Kohana::$config->load('massload/bycategory.'.$category->value);
    			$categories[$category->value] = $cfg["name"];
    		} catch(Exception $e){}
    	}
    	$this->template->categories = $categories;

    }

    public function action_massload_conformities()
    {
    	$this->layout = 'users';    	
//    	$this->assets->js('http://yandex.st/underscore/1.6.0/underscore-min.js');
    	$this->assets->js('http://yandex.st/backbone/1.1.2/backbone-min.js');
    	$this->assets->js('massload_set.js');

    	$categories = Array();
    	$category = $this->request->param('category');
    	if(!$category)
    		throw new HTTP_Exception_404;

    	$user = $this->user;
    	$user_id = $this->request->param('user_id');
    	$this->template->end_user_id = $user_id;
    	
    	if ($this->user->role == 1 AND $user_id)
    		$user = ORM::factory('User', $user_id);

    	try 
		{ 
			$cfg = Kohana::$config->load('massload/bycategory.'.$category);
			$categories[$category] = $cfg["name"];
		} catch(Exception $e){
			throw new HTTP_Exception_404;
		}
    	$this->template->categories = $categories;

    	$conformities = Array();
    	$dictionaries = Array();
    	$forms = Array();
    	foreach ($categories as $key=>$value)
    	{
    		$cfg = Kohana::$config->load('massload/bycategory.'.$key);
    		@list($dictionary, $form) = Massload::get_dictionary($cfg, $user->id, $key);
    		$dictionaries[$key] = $dictionary;
    		$forms[$key] = $form; 
	    }
	    $this->template->dictionaries 	= $dictionaries;
	    $this->template->forms 			= $forms;
    	$this->template->conformities 	= $conformities;
    	$this->template->cfg 			= $cfg;

    }

    public function action_plan()
    {
    	$this->layout = 'users'; 
    	$services = Array();  
    	$plans = ORM::factory('Plan')->find_all(); 	
    	foreach($plans as $plan)
    	{
    		$services[] = ORM::factory('Service')->where("options","=",$plan->name."_".$plan->number)->find();
    	}

    	$this->template->user_plans = ORM::factory('User_Plan')
				    					->where("user_id","=",$this->user->id)
				    					->where("date_expiration",">",'NOW()')
				    					->find_all(); 	
    	$this->template->services = $services;
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
		$this->layout = 'myads';
		$this->assets->js('myads.js');
		$this->assets->js('jquery.flot.min.js');
		$this->assets->js('jquery.flot.time.min.js');

		// pagination settings
		$per_page	= 20;
		$page		= (int) Arr::get($_GET, 'page', 1);

		// get objects
		$objects = ORM::factory('Object')
			->set_time_link_cache(15)
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
		$region		= ORM::factory('Region', intval($this->request->query('region_id')))->cached(DATE::WEEK, array("category", "myads"));
		$city		= ORM::factory('City', intval($this->request->query('city_id')))->cached(DATE::WEEK, array("category", "myads"));
		$category	= ORM::factory('Category', intval($this->request->query('category_id')))->cached(DATE::WEEK, array("category", "myads"));

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
		$count = $count->count_all(NULL, DATE::HOUR);

		// get user objects
		$objects = $objects->order_by('date_created', 'desc')
			->limit($per_page)
			->offset($per_page*($page-1))
			->find_all();

		//get balance for premium ads				
		
		$premium_balance = (int) Service_Premium::get_balance($this->user);
		$this->template->premium_balance = $premium_balance;

		$already_buyed = Service_Premium::get_already_buyed($this->user);
		$this->template->already_buyed = $already_buyed;

		$cities 	= array();
		$categories = array();

		// get user objects categories		
		$cities = DB::select(DB::expr('COUNT(object.id)'))
					->select('city.id')->select('city.title')
					->from('object')
						->join('city')->on('object.city_id', '=', 'city.id')
					->where('author', '=', $this->user->id)
					->where('active', '=', 1);
		
		if ($category->loaded())
		{
			$cities = $cities->where('category', '=', $category->id);
		}

		$cities = $cities->group_by('city.id')
					->group_by('city.title')
					->order_by('city.title')
					->as_object()
					->cached(DATE::WEEK)
					->execute();

		$this->template->cities = $cities;

		// get user objects categories
		$categories = DB::select(DB::expr('COUNT(object.id)'))
					->select('category.id')->select('category.title')
					->from('object')
						->join('category')->on('object.category', '=', 'category.id')
					->where('author', '=', $this->user->id)
					->where('active', '=', 1);

		if ($city->loaded())
		{
			$categories = $categories->where('city_id', '=', $city->id);
		}

		$categories = $categories->group_by('category.id')
					->group_by('category.title')
					->order_by('category.title')
					->as_object()
					->cached(DATE::WEEK)
					->execute();

		$this->template->categories = $categories;

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
			->cached(DATE::WEEK, array("city", "myads"))
			->find_all();

		$this->template->objects = $objects;
		$this->template->service_promo_link = ORM::factory('Service')->where('name', '=', 'promo_link')->cached(DATE::WEEK, array("service", "myads"))->find();
		$this->template->service_promo_link_bg = ORM::factory('Service')->where('name', '=', 'promo_link_bg')->cached(DATE::WEEK, array("service", "myads"))->find();
		$this->template->running_line_site_s = ORM::factory('Service')->where('name', '=', 'running_line_site_s')->cached(DATE::WEEK, array("service", "myads"))->find();
		$this->template->service_premium 		= ORM::factory('Service')->where('name', '=', 'premium_ads')->cached(DATE::WEEK, array("service", "myads"))->find();
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

	public function action_employers()
	{
		$user = Auth::instance()->get_user();

		if ($user->org_type <> 2){
			$this->redirect("/user/userinfo");
		}
		if ($user->org_moderate <> 1){
			$this->redirect("/user/orginfo");
		}
		if ($user->linked_to_user) {			
			$this->template = View::factory('user/ischilduser', array("company" => ORM::factory('User',$user->linked_to_user),
																		"name" => "Информация о компании"));
			return;
		}

		$error = NULL;
		
		$is_post = (HTTP_Request::POST === $this->request->method());
		$email = trim(mb_strtolower($this->request->post('email')));		
		$method = $this->request->query('method');
		$actionuser_id = (int) $this->request->query('id');		

		
		if ($is_post AND $method == "link")
		{
			$childuser_add = ORM::factory('User')
								->where("email","=",mb_strtolower($email))
								->where("id","<>",$user->id)
								->find()
								->link_user($user->id);

			if (!$childuser_add)
			{
				$this->redirect("/user/employers?success=1");
			} else {
				$error = $childuser_add;
			}

		} elseif ($method == "accept_request"){

			$childuser = ORM::factory('User',$actionuser_id);
			$childuser_add = $childuser->link_user($user->id, TRUE);
			if (!$childuser_add)
			{
				$msg = View::factory('emails/user_manage/accept_request_to_link_company', 
					array(
						'request_user' => $user,
					)
				);
				Email::send($childuser->email, Kohana::$config->load('email.default_from'), "Привязка к компании ".$user->org_name." подтверждена", $msg);
				
				ORM::factory('User_Link_Request')->delete_request($user->id, $actionuser_id);
				$this->redirect("/user/employers?success=1");
			} else {
				$error = $childuser_add;
			}	

		} elseif ($method == "unlink"){

			ORM::factory('User')->unlink_user($user->id, $actionuser_id);

		} elseif ($method == "decline_request"){
			$childuser = ORM::factory('User',$actionuser_id);
			$msg = View::factory('emails/user_manage/decline_request_to_link_company', 
					array(
						'request_user' => $user,
					)
				);
			Email::send($childuser->email, Kohana::$config->load('email.default_from'), "Привязка к компании ".$user->org_name." НЕ подтверждена", $msg);
				
			ORM::factory('User_Link_Request')->decline_request($user->id, $actionuser_id);				

		}

		$this->template->is_post = $is_post; 
		$this->template->error = $error;
		$this->template->users = ORM::factory('User')
										->where("linked_to_user","=", $user->id)
										->find_all();

		$this->template->requests = ORM::factory('User_Link_Request')
										->where("user_id","=", $user->id)
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

		$region = ORM::factory('Region')->where('id', '=', 73)->find();
		$city	= Region::get_current_city();
		//Наличие объявлений у пользователя
		$is_exist_objects = 0;
		
		if ( ! $user->loaded() or $user->org_type != 2 )
		{
			throw new HTTP_Exception_404;
		}
		
		$user_settings = ORM::factory('User_Settings')
				->where('user_id', '=', $user->id)
				->where('type', '=', 'orginfo')
				->find_all();
		
		$user_settings = Dbhelper::convert_dbset_to_keyid_arr($user_settings, 'name');
		
		$job_category_id = 36;//TODO: Костыль: Пропись id

		$objects = ORM::factory('Object')
				->where('author_company_id', '=', $user)
				->where('active', '=', 1)
				->where('is_published', '=', 1)
				->where('category', '=', $job_category_id)
				->where('date_expired', '<=',  DB::expr('CURRENT_TIMESTAMP'));
		
		
		
		if ($city) 
			$objects->where('city_id', '=', $city->id);
		
		$this->template->job_adverts_count = $is_exist_objects = $job_adverts_count = $objects->count_all();
		//Если объявлений нет хотя бы в вакансиях, то смотрим наличие остальных
		if (!$is_exist_objects)
		{
			$objects = ORM::factory('Object')
				->where('author_company_id', '=', $user)
				->where('active', '=', 1)
				->where('is_published', '=', 1)
				->where('date_expired', '<=',  DB::expr('CURRENT_TIMESTAMP'));		
		
			if ($city) 
				$objects->where('city_id', '=', $city->id);
			
			$is_exist_objects = $objects->count_all();
		}
		
		$this->template->user_settings = $user_settings;
		$this->template->is_exist_objects = $is_exist_objects;
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
		$this->template->region = $region;
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
			$this->json['filename_big'] = Uploads::get_file_path($filename, '136x136');
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

	public function action_password()
	{
		$error = NULL;

		if (HTTP_Request::POST === $this->request->method())
		{
			$validation = Validation::factory($_POST)
				->rule('password', 'not_empty')
				->label('password', 'Пароль')
				->rule('password', 'matches', array(':validation', 'password', 'password_repeat'));

			if ($validation->check())
			{
				$this->user->passw = trim($this->request->post('password'));
				$this->user->save();

				Session::instance()->set('success', TRUE);
			}
			else
			{
				$error = join(',', $validation->errors('validation/password'));
			}
		}

		$this->template->error = $error;
	}

	public function action_registration()
	{
		$this->layout = 'auth';
		$is_post = (HTTP_Request::POST === $this->request->method());
		$post_data = new Obj($this->request->post());
		$error = new Obj();
		$success = FALSE;
		$token = NULL;
		if ($is_post)
		{
			$post_data->login = strtolower(trim($post_data->login));
			$token = $post_data->csrf;
			$validation = ORM::factory('User')
								->register_validation((array) $post_data);

			if ( !$validation->check())
			{
				$error = new Obj($validation->errors('validation/auth'));
			} else {
				try {

					$user_id = ORM::factory('User')
									->registration( $post_data->login, 
													$post_data->pass, 
													$post_data->type );
				} catch (Exception $e)
				{
					$error->login = "Произошла непредвиденная ошибка. Информация о ошибке отправлена администратору.";

					Admin::send_error("Ошибка при регистрации пользователя", array(
							$e->getMessage(), Debug::vars($post_data), $e->getTraceAsString()
					));
				}
			}
		} else {
			$token = Security::token();
		}

		$limited_categories = ORM::factory('Category')
					->where("max_count_for_user",">",0)
					->cached(Date::DAY)
					->find_all();

		$this->template->token = $token;
		$this->template->limited_categories = $limited_categories;
		$this->template->captcha = Captcha::instance()->render();
		$this->template->success = (isset($user_id));
		$this->template->params = $post_data;
		$this->template->error = $error;
		$this->template->auth = Auth::instance()->get_user();
	}

	public function action_account_verification()
	{
		$this->layout = 'auth';
		$code =$this->request->param("id");
		$user = ORM::factory('User')
						->where("code","=",trim($code))
						->where("is_blocked","=",2)->find();

		if ($user->loaded())
		{
			$user->delete_code();
			$contact = ORM::factory('Contact')
							->by_contact_and_type($user->email, Model_Contact_Type::EMAIL)
							->find();

			$contact->contact = $user->email;
			$contact->contact_type_id = Model_Contact_Type::EMAIL;
			$contact->verified_user_id = $user->id;
			$contact->show = 1;
			$contact->moderate = 1;
			$contact->save();

			$user_contact = ORM::factory('User_Contact');
			$user_contact->user_id = $user->id;
			$user_contact->contact_id = $contact->id;
			$user_contact->save();

			Auth::instance()->trueforcelogin($user);
			$this->template->message = "Добро пожаловать! Вы успешно зарегистрировались";
			$this->template->success = TRUE;
			$this->template->redirectTo = "http://".Kohana::$config->load("common.main_domain");

		} else {
			$this->template->success = FALSE;
			$this->template->message = "Ссылка устарела, либо вы уже активировали эту учетную запись ранее. ";
		}

	}

	public function action_login()
	{
		$this->layout = 'auth';
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = new Obj($this->request->post());

		$return_page = Arr::get($_GET, 'return', "");
		$domain = Arr::get($_GET, 'domain', NULL);
		if (!$domain)
			$domain = Url::base('http');
		else
			$domain = "http://".Kohana::$config->load("common.main_domain");

		
		$token = NULL;
		$error = NULL;
		$success = NULL;
		if ($is_post){
			$token = $post_data->csrf;
			$validation = Validation::factory((array) $post_data)
					->rule('csrf', 'not_empty', array(':value', "CSRF"))
					->rule('csrf', 'Security::check');


			if ( !$validation->check())
			{
				$error = new Obj($validation->errors('validation/auth'));
			} else {
				$auth = Auth::instance();
				try {
					$auth->login($post_data->login, $post_data->pass, TRUE);
					
				} 
					catch (Exception $e)
				{
					$error = $e->getMessage();

				} 

				if (!$error)
						$this->redirect($domain.$return_page);
			}
		} else {
			$token = Security::token();
			if ($this->user AND $return_page)
			{
				Auth::instance()->trueforcelogin($this->user);
				$this->redirect($domain.$return_page);
			}
		}

		$this->template->token = $token;
		$this->template->user = $this->user; 
		$this->template->params = $post_data;
		$this->template->error = $error;

	}

	public function action_forgot_password()
	{
		$this->layout = 'auth';
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = new Obj($this->request->post());

		$error = NULL;
		if ($is_post){
			$email = mb_strtolower(trim($post_data->email), 'UTF-8');
			if (!$email)
			{
				$error = "Вы не ввели email";
			} else {
				$user = ORM::factory('User')
							->get_user_by_email($email)
							->find();
				if (!$user->loaded())
				{
					$error = "Этот email не зарегистрирован";
				} elseif ($user->is_blocked == 1)
				{
					$error = "Этот email заблокирован, за нарушение правил сайта";
				}
				else 
				{
					$code = $user->create_forgot_password_code();
					$url  = URL::base('http')."user/forgot_password_link/".$code;
					$msg = View::factory('emails/forgot_password', array('url' => $url));
					Email::send($user->email, Kohana::$config->load('email.default_from'), 'Восстановление пароля', $msg);
					$this->redirect(URL::base('http').'user/forgot_password?success=1');
				}
			}
		} 

		$this->template->status = NULL;
		
		$success = $this->request->query('success');
		$failure = $this->request->query('failure');
		if ($success)
			$this->template->status = "success";
		if ($failure)
			$this->template->status = "failure";

		$this->template->error = $error;
		$this->template->params = $post_data;
		$this->template->user = $this->user; 
	}

	public function action_forgot_password_link()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;

		$code = trim($this->request->param('id'));
		if (!$code)
			throw new HTTP_Exception_404;

		$user = ORM::factory('User')
					->get_user_by_code(trim($code))
					->find();
		if (!$user->loaded())
			$this->redirect(URL::base('http').'user/forgot_password?failure=1');
		else 
		{
			$user->delete_code();
			Auth::instance()->trueforcelogin($user);
			$this->redirect(URL::base('http').'user/password');
		}

	}

	public function action_logout()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$_main = (int)$this->request->query('main');

		if (Auth::instance()->get_user())
		{
			setcookie('user_id', '', time()-1, '/', Region::get_cookie_domain());
			Auth::instance()->logout();
		}

		$main = "";
		if ($_main)
			$main="?main=1";

		$this->redirect('http://'.Region::get_current_domain().'/user/logout'.$main);
	}

	public function action_orginfo()
	{

		$this->assets->js("ajaxupload.js");

		$user = Auth::instance()->get_user();

		if ($user->org_type <> 2){
			$this->redirect("/user/userinfo");
		}
		if ($user->linked_to_user) {			
			$this->template = View::factory('user/ischilduser', array("company"=> ORM::factory('User', $user->linked_to_user),
																		"name" => "Информация о компании"));
			return;
		}

		$is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
		$data = $inn =NULL;
		$errors = new Obj();

		$form = Form_Custom::factory("Orginfo");

		$settings = new Obj(ORM::factory('User_Settings')
								->get_group($user->id, "orginfo"));
		if ($user->org_inn)
		{
			unset($form->_settings["fields"]["INN"]);
			unset($form->_settings["fields"]["INN_photo"]);
			unset($form->_settings["fields"]["org_full_name"]);
			$inn_skan = Imageci::getSitePaths($user->org_inn_skan);
			$inn = array(
					"inn" 	 		=> $user->org_inn,
					"org_full_name"	=> $user->org_full_name,
					"inn_skan" 		=> $inn_skan["120x90"]
				);
		} 

		$inn_moderate = array(
			"inn_moderate" 			=> $user->org_moderate,
			"inn_moderate_reason" 	=> $settings->{"moderate-reason"}
		);

		if ($is_post)
		{
			$data 		= $this->request->post();

			if (isset($data["INN"])){
				$parentuser = ORM::factory('User')
								->where("org_moderate","=",1)
								->where("org_inn","=",$data["INN"])
								->where("id","<>",$user->id)
								->find();

				if ($parentuser->loaded())
					$this->redirect("/user/user_link_request?inn=".$data["INN"]);
			}

			$form->save($data);
			if ($form->errors)
			{
				$errors = new Obj($form->errors);	
			} 
			else 
			{
				if ( array_key_exists("INN", $data) )
				{
					//прописываем инн, скан и юр имя организации в User
					$user->org_inn = $data["INN"];
					$user->org_inn_skan = $data["INN_photo"];
					$user->org_full_name = $data["org_full_name"];
					$user->org_moderate = 0;

					//ставим на модерацию
					ORM::factory('User_Settings')
						->update_or_save($user->id, "orginfo", "moderate", 0);

					//удаляем причину модерации, если она была проставлена ранее
					ORM::factory('User_Settings')
						->_delete($user->id, "orginfo", "moderate-reason");
				}

				$user->org_name 		= $data["org_name"];
				$user->org_post_address = $data["mail_address"];
				$user->org_phone 		= $data["phone"];
				$user->about = $data["commoninfo"];
				$user->filename = ORM::factory('User_Settings')
										->where("user_id","=",$user->id)
										->where("name","=","logo")
										->where("type","=","orginfo")
										->find()
										->value;
				try {
					$user->save();
				} catch (Exception $e) {
					Admin::send_error("Ошибка при сохранении ИНН", array(
							$e->getMessage(), Debug::vars($data), $e->getTraceAsString()
					));
				}

				$this->redirect('/user/orginfo?success=1');
			}
		}
		else 
		{
			$data = $form->get_data();		
		}

		$this->template->expired = $settings->{"date-expired"};
		$this->template->from = $this->request->query("from");
		$this->template->form = $form->prerender($data);
		$this->template->data = new Obj($data);
		$this->template->errors = $errors;
		$this->template->inn = $inn;
		$this->template->inn_moderate = $inn_moderate;
		$this->template->success = $this->request->query("success");
		$this->template->org_moderate_states = Kohana::$config->load("dictionaries.org_moderate_states");
	}

	public function action_userinfo()
	{
		$user = Auth::instance()->get_user();
		$is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
		$data = NULL;
		$errors = new Obj();
		$form = Form_Custom::factory("Userinfo");


		if ($is_post)
		{
			$data = $this->request->post();
			$form->save($data);
			if ($form->errors)
			{
				$errors = new Obj($form->errors);
			} else {
				$user->fullname = $data["contact_name"];
				$user->save();
			}
		}
		else 
			$data = $form->get_data();	

		$this->template->categories_limit = ORM::factory('Category')
												->get_limited()
												->find_all();

		$this->template->individual_limit = ORM::factory('Category')
												->get_individual_limited($user->id);

		$this->template->request_company = ORM::factory('User_Link_Request')
												->where("linked_user_id","=",$user->id)
												->find();	

		$this->template->types = Kohana::$config->load("dictionaries.org_types");
		$this->template->user = $user;
		$this->template->form = $form->prerender($data);
		$this->template->errors = $errors;
		$this->template->parent_user = ORM::factory('User', $user->linked_to_user);
	}

	public function action_edit_ad()
	{
		$this->layout = 'add';
		
		$this->assets->js("nicEdit.js");

		$prefix = (@$_SERVER['HTTP_HOST'] === 'c.yarmarka.biz') ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
//		$this->assets->js($staticfile->jspath);

		$errors = new Obj();
		$token = NULL;
		$object_id = (int)$this->request->param('object_id');
		$object = ORM::factory('Object', $object_id);
		if (!$object_id OR !$object->loaded())
    		throw new HTTP_Exception_404;

    	if ($object->author <> $this->user->id AND !in_array($this->user->role, array(1,9,3)))
    		throw new HTTP_Exception_404;

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = $this->request->post();

		if ($is_post) {  
			$errors = new Obj(Object::default_save_object($post_data));
			if ($errors->error){
				$errors = new Obj($errors->error);
			}
			else 
			{
				$return_object_id = $errors->object_id;
				$this->redirect('http://'.Region::get_current_domain().'/detail/'.$return_object_id);
			}	
		
			$params = $post_data;
			$token = (isset($post_data["csrf"])) ? $post_data["csrf"] : NULL;
		} 
		else
		{
			$token = Security::token();
			$params = array(
				'object_id'		=> $object_id
			);
		}

		$form_data = new Form_Add($params, $is_post, $errors);

		$user = Auth::instance()->get_user();
		if (!$user)
			$form_data->Login();

		$form_data	->Category()
				 	->City()
				 	->Subject()
				 	->Text()
				 	->Photo()
				 	->Video()
				 	->Params()
				 	->Map()
				 	->Price()
				 	->Contacts()
				 	->Additional();

		if ($user AND $user->role == 9)
			$form_data ->AdvertType();

		if ($user AND $user->org_type == 2)
			$form_data->OrgInfo();
		elseif ($user AND $user->linked_to_user)
			$form_data ->LinkedUser();

		$this->template->token = $token;
		$this->template->set_global('jspath', $staticfile->jspath);
		$this->template->object  = $object;
		$this->template->params 	= new Obj($params);
		$this->template->form_data 	= $form_data->_data;
		$this->template->errors = (array) $errors;
		$this->template->assets = $this->assets;
		$this->template->user = ($user AND $user->loaded()) ? $user->org_type : "undefined";

		$expired = NULL;
		if (!$user->is_valid_orginfo())
		{
			$settings = new Obj(ORM::factory('User_Settings')->get_group($user->id, "orginfo"));
			$expired =  $settings->{"date-expired"};

		}
		$this->template->expired_orginfo = $expired;
	}

	public function action_message()
	{
		$message =  $this->request->query('message');

		if ($message == 'userblock')
			$message = "Ваша учетная запись была заблокирована, по причине: ".$this->user->block_reason;		$this->template->message = $message;
	}

	public function action_send_report()
	{
		$this->use_layout	= FALSE;
		$this->auto_render	= FALSE;
		$objectload_id = (int)$this->request->param('id');

		$objectload = new Objectload(NULL, $objectload_id);
		$objectload->sendReport($objectload_id);

	}

	public function action_reset_orgtype()
	{
		$user = Auth::instance()->get_user();

		ORM::factory('User',$user->id)->reset_orgtype();

		$this->redirect('/user/userinfo');
	}

	public function action_reset_to_company()
	{
		$user = Auth::instance()->get_user();

		ORM::factory('User',$user->id)->reset_to_company();

		$this->redirect('/user/orginfo');
	}

	public function action_reset_parent_user()
	{
		$user = Auth::instance()->get_user();

		ORM::factory('User',$user->id)->reset_parent_user();

		$this->redirect('/user/userinfo');
	}

	public function action_user_link_request()
	{
		$user = Auth::instance()->get_user();

		$method = $this->request->query("method");
		$parentuser_inn = $this->request->query("inn");
		$parentuser_email = $this->request->query("email");

		if ($method == "delete_request")
		{
			ORM::factory('User_Link_Request')
				->delete_requests($user->id);

			$this->redirect("/user/userinfo");
		}
		if (!$parentuser_inn AND !$parentuser_email)
			$this->redirect("/user/orginfo");

		$request_type = NULL;
		if ($parentuser_email)
		{
			$parentuser = ORM::factory('User')
								->where("org_moderate","=",1)
								->where("email","=",trim(mb_strtolower($parentuser_email)))
								->where("id","<>",$user->id)
								->find();
			$request_type = "email";
		} else {
			$parentuser = ORM::factory('User')
								->where("org_moderate","=",1)
								->where("org_inn","=",$parentuser_inn)
								->where("id","<>",$user->id)
								->find();
			$request_type = "inn";
		}

		if (!$parentuser->loaded())
			$this->redirect("/user/userinfo");

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$ulr = NULL;
		if ($is_post) { 
			$parentuser_id = $this->request->post("id");

			$ulr = ORM::factory('User_Link_Request')
						->where("linked_user_id","=",$user->id)
						->find();
			$ulr->user_id = $parentuser_id;
			$ulr->linked_user_id = $user->id;
			$ulr->save();

			$msg = View::factory('emails/user_manage/request_to_link_company', 
				array(
					'request_user' => $user,
				)
			);
			Email::send($parentuser->email, Kohana::$config->load('email.default_from'), "Запрос на разрешение подачи объявлений от лица вашей компании", $msg);
		} else {
			$ulr = ORM::factory('User_Link_Request')
						->where("linked_user_id","=",$user->id)
						->find();
		}

		$this->template->request_type = $request_type;
		$this->template->inn = $parentuser_inn;
		$this->template->email = $parentuser_email;
		$this->template->parentuser = $parentuser;
		$this->template->request = $ulr;
	}
}
/* End of file User.php */
/* Location: ./application/classes/Controller/User.php */