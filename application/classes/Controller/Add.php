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
		}
	}

	public function action_index()
	{
		$this->layout = 'add';
		//$this->assets->js(Url::base(TRUE).'static');
		//
		$user = Auth::instance()->get_user();

		if ($user AND !Cookie::get('authautologin'))
				Auth::instance()->trueforcelogin($user);
		
		$prefix = (@$_SERVER['HTTP_HOST'] === 'c.yarmarka.biz') ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
		echo Assets::factory('main')->js($staticfile->jspath);

		$errors = new Obj();

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = $this->request->post();

		if ($is_post) {  
			$errors = new Obj(self::action_native_save_object($post_data));
			if ($errors->error)
				$errors = new Obj($errors->error);
			else {
				$object_id = $errors->object_id;
				$this->redirect('http://'.Region::get_current_domain().'/billing/services_for_ads/'.$object_id."?afteradd=1");
			}

			$params = $post_data;
		} 
		else
		{
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
				 	->Contacts();

		if ($user AND $user->role == 9)
			$form_data ->AdvertType();

		$this->template->params 	= new Obj($params);
		$this->template->form_data 	= $form_data->_data;
		$this->template->errors = (array) $errors;
		$this->template->assets = $this->assets;

	}

	public function action_native_save_object($params = NULL)
	{
		if (!$params)
			$params = $this->request->post();
		return Object::default_save_object($params);
	}

	public function action_save_object()
	{
		
		$this->auto_render = FALSE;
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

		$this->response->body(json_encode($json));
	}

}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */