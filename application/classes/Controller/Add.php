<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function before()
	{
		parent::before();

		$user = Auth::instance()->get_user();
		if ($user)
		{
			$user->reload();
			if ($user->is_blocked == 1)
			{
				$this->redirect(Url::site('user/message?message=userblock'));
			}

			if (!$user->is_valid_orginfo()
					AND in_array(Request::current()->action(), array('index')))
			{
				if ($user->is_expired_date_validation())
					HTTP::redirect("/user/orginfo?from=another");
			}
		}
	}

	public function action_index()
	{
		$this->use_layout   = FALSE;
		$this->auto_render  = FALSE;
		$twig = Twig::factory('user/add');
		$twig->params = new Obj();
		
		$prefix = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
		$twig->data_file = $staticfile->jspath;

		$twig->crumbs = array(
			array("title" => "Создание объявления"),
		);
		
		$user = Auth::instance()->get_user();

		if ($user AND !Cookie::get('authautologin'))
				Auth::instance()->trueforcelogin($user);
		
		$prefix = (@$_SERVER['HTTP_HOST'] === 'c.yarmarka.biz') ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');

		$errors = new Obj();
		$token = NULL;
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = $this->request->post();

		if ($is_post) {  
			$errors = new Obj(self::action_native_save_object($post_data));
			if ($errors->error)
				$errors = new Obj($errors->error);
			else {
				$object_id = $errors->object_id;
				$this->redirect('/detail/'.$object_id."?afteradd=1");
			}

			$params = $post_data;
			$token = (isset($post_data["csrf"])) ? $post_data["csrf"] : NULL;
		} 
		else
		{
			$token = Security::token();
			$params = array(
				'rubricid'		=> (int)$this->request->param('rubricid'),
				//'object_id'		=> (int)$this->request->query('object_id'),
				'city_id'		=> (int)$this->request->param('city_id')
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
					->Widgets()
					->Additional();
					
		if ($user AND $user->org_type == 2)
			$form_data->OrgInfo();
		elseif ($user AND $user->linked_to_user)
			$form_data->LinkedUser();

		if ( Acl::check("object.add.type") )
			$form_data ->AdvertType();

		if ( Acl::check("object.add.type") )
			$form_data ->UserType();

		$twig->params->token = $token;
		
		//$twig->template->set_global('jspath', $staticfile->jspath);

		$twig->params->params 	= new Obj($params);
		$twig->params->form_data 	= $form_data->_data;
		$twig->params->errors = (array) $errors;
		$twig->params->assets = $this->assets;
		$twig->params->user = ($user AND $user->loaded()) ? $user->org_type : "undefined";

		$expired = NULL;
		if ($user AND !$user->is_valid_orginfo()
					AND !$user->is_expired_date_validation())
		{
			$settings = new Obj(ORM::factory('User_Settings')->get_group($user->id, "orginfo"));
			$expired =  $settings->{"date-expired"};
		}
		$twig->params->expired_orginfo = $expired;

		$twig->params = (array) $twig->params;
		$twig->block_name = "add/_index";
		$this->response->body($twig);

	}

	public function action_edit_ad()
	{
		$this->use_layout   = FALSE;
		$this->auto_render  = FALSE;
		$twig = Twig::factory('user/add');
		$twig->params = new Obj();

		$user = Auth::instance()->get_user();
		
		$prefix = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
		$twig->data_file = $staticfile->jspath;

		$twig->crumbs = array(
			array("title" => "Создание объявления"),
		);

		$errors = new Obj();
		$token = NULL;
		$object_id = (int)$this->request->param('object_id');
		$object = ORM::factory('Object', $object_id);
		if (!$object_id OR !$object->loaded())
			throw new HTTP_Exception_404;

		if (!$user OR ($object->author <> $user->id AND !in_array($user->role, array(1,9,3))) )
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
				$this->redirect('/detail/'.$return_object_id);
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

		if ( Acl::check("object.add.type") )
			$form_data ->AdvertType();

		if ( Acl::check("object.add.type") )
			$form_data ->UserType();

		if ($user AND $user->org_type == 2)
			$form_data->OrgInfo();
		elseif ($user AND $user->linked_to_user)
			$form_data ->LinkedUser();

		$twig->params->token = $token;
		$twig->params->object  = $object;
		$twig->params->params 	= new Obj($params);
		$twig->params->form_data 	= $form_data->_data;
		$twig->params->errors = (array) $errors;
		$twig->params->assets = $this->assets;
		$twig->params->user = ($user AND $user->loaded()) ? $user->org_type : "undefined";

		$expired = NULL;
		if (!$user->is_valid_orginfo())
		{
			$settings = new Obj(ORM::factory('User_Settings')->get_group($user->id, "orginfo"));
			$expired =  $settings->{"date-expired"};

		}
		$twig->params->expired_orginfo = $expired;
		$twig->params = (array) $twig->params;
		$twig->block_name = "add/_index";
		$this->response->body($twig);
	}

	public function action_native_save_object($params = NULL)
	{
		if (!$params)
			$params = $this->request->post();
		return Object::default_save_object($params);
	}

	public function action_save_object()
	{
		
		/*$this->auto_render = FALSE;
		$json = array();
		$user = Auth::instance()->get_user();

		if (!$user){
			$json['error'] = 'Ошибка авторизации. Необходимо очистить `cookie` (куки) браузера.';
			$this->response->body(json_encode($json));
			return;
		}

		if ( ! Request::current()->is_ajax())
		{
			//throw new HTTP_Exception_404('only ajax requests allowed');
		}

		if ($this->request->post("only_run_triggers") == 1)
		{
			$json = Object::PlacementAds_JustRunTriggers($this->request->post());
			$this->response->body(json_encode($json));
			return;
		}

		//если в локале работаем с подачей, ставим 1
		$local = Kohana::$config->load('common.is_local');

		if ($local == 1)
		{
			//подставляется дефолтный город 1947 не из кладра, чтоб кладр на локале не разворачивать
			//не сохраняется короткий урл
			$json = Object::PlacementAds_Local($this->request->post());
			$this->response->body(json_encode($json));
			return;
		} 


		if ($user->role == 9) 
			//убрана проверка контактов
			//убрана проверка на максимальное количество объяв в рубрику
			$json = Object::PlacementAds_ByModerator($this->request->post());
		else 
			//сохранение по дефолту
			$json = Object::PlacementAds_Default($this->request->post());

		$this->response->body(json_encode($json));*/
	}
	
	
	public function action_object_upload_file()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->json['code'] = 200; // code by default
		
		try
		{
			if (empty($_FILES['userfile1']))
		{
			throw new Exception('Не загружен файл');
		}

		$filename = Uploads::make_thumbnail($_FILES['userfile1']);

		$check_image_similarity = Kohana::$config->load('common.check_image_similarity');

		if ($check_image_similarity)
		{
			$similarity = ORM::factory('Object_Attachment')->get_similarity(Uploads::get_full_path($filename));
			if ($similarity > Kohana::$config->load('common.max_image_similarity'))
			{
				throw new Exception('Фотография не уникальная, уже есть активное объявление с такой фотографией либо у вас , либо у другого пользователя');
			}
		}

		$this->json['filename'] = $filename;
		$this->json['filepaths'] = Imageci::getSitePaths($filename);

		$tmp_img = ORM::factory('Tmp_Img');
		$tmp_img->name = $filename;
		$tmp_img->save();
		}
			catch(Exception $e)
		{
			$this->json['error'] = $e->getMessage();
		}
		
		
		$this->response->body(json_encode($this->json));
	}
}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */