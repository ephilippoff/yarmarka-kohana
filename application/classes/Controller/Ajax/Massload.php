<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Massload extends Controller_Template {

	protected $json = array();

	public function before()
	{
		// only ajax request is allowed
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			//throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		parent::before();

		$this->json['code'] = 200; // code by default
	}

	public function action_massedit()
	{
		$limit = (int) $this->request->query("limit");
		$objects = ORM::factory('Object')
								->where("category","=",3)
								->where("active","=",1)
								->where("is_published","=",1)
								->where("parent_id","", DB::expr('IS NULL'))
								->order_by("date_created", "desc")
								->limit($limit)
								->find_all();
		$json = Array();
		foreach ($objects as $item)
		{
			$json[] = Object::PlacementAds_JustRunTriggers(Array("object_id" => $item->id, "only_run_triggers" => 1));
		}

		$this->json['data'] = $json;
	}

	public function action_checkfile()
	{

		$category_id 	= $this->request->post("category_id");
		$user_id 		= $this->request->post("user_id");
		$ignore_errors 	= (int) $this->request->post("ignore_errors");

		$user = Auth::instance()->get_user();
		if ($user->role == 1 AND $user_id)
		{
			$user = ORM::factory('User', $user_id);
		}

		$file = $_FILES["file"];
		if (empty($user)) {
			$this->json['critError'] = 'Пользователь не определен';
			return;
		} elseif (empty($file)) {
			$this->json['critError'] = 'Не загружен файл';
			return;
		} elseif (!$category_id){
			$this->json['critError'] = 'Не указана категория';
			return;
		}
		
		$ml = new Massload();

		try {
			@list($filepath, $imagepath, $errors, $count) = $ml->checkFile($file, $category_id, $user->id);
		} catch (Exception $e) {
			$this->json['count'] 		= "?";
			$this->json['errorcount'] 	= "?";
			$this->json['errors'] = Array($e->getMessage());
			return;
		} 

		if ($ignore_errors == 0 AND count($errors)>0)
		{
			$this->json['count'] 		= $count;
			$this->json['errorcount'] 	= count($errors);
			$this->json['errors'] 		= $errors;
			return;
		}

		$this->json['user_id'] 		= $user->id;   
	    $this->json['pathtofile'] 	= $filepath;
		$this->json['pathtoimage']  = $imagepath;
		$this->json['count'] 		= $count;
		$this->json['errorcount'] 	= count($errors);
		$this->json['errors'] 		= $errors;
		$this->json['data'] = 'ok';
	}

	public function action_load_next_strings()
	{
		$pathtofile 	= (string) $this->request->post('pathtofile');
		$pathtoimage 	= (string) $this->request->post('pathtoimage');
		$row_num 		= (int) $this->request->post('row_num');
		$category_id 	= $this->request->post('category_id');
		$user_id 		= $this->request->post("user_id");

		$this->json['category_id'] = $category_id;

		if (!$category_id)
			return;

		$user = Auth::instance()->get_user();
		if ($user->role == 1 AND $user_id)
		{
			$user = ORM::factory('User', $user_id);
		}

		$ml = new Massload();

		$this->json['data'] = $ml->saveStrings($pathtofile, $pathtoimage, $category_id, $row_num, $user->id);
	}

	public function action_conformity()
	{
		$response = json_decode($this->request->body());
		$user = Auth::instance()->get_user();
		if ($user->role == 1 AND $response->user_id)
		{
			$user = ORM::factory('User', $response->user_id);
		}
		
		if (trim($response->conformity) == ""){
			
			$cf = ORM::factory('User_Conformities')->delete_conformity($user->id, $response->massload, $response->type, $response->value);
			$this->json['data'] ="ok";
		} else {
			$cf = ORM::factory('User_Conformities')
						->where('type', '=', $response->type)
						->where('value', '=', $response->value)
						->where('massload', '=', $response->massload)
						->where('user_id', '=', $user->id)
						->find();
			$cf->user_id  	= $user->id;
			$cf->massload  	= $response->massload;
			$cf->type  		= $response->type;
			$cf->value  	= $response->value;
			$cf->conformity = $response->conformity;
			$cf->save();

			if ($cf->id >0)
				$this->json['data'] ="ok";
			else 
				$this->json['data'] ="Не удалось сохранить";
		}
		
	}

	public function action_save_staticfile()
	{
		$category 		= $this->request->post("category");
		$user_id 		= $this->request->post("user_id");
		$file = $_FILES["file"];
		$db = Database::instance();
		if (!$category)
			return;

		$ol = new Objectload($user_id);
		$ol->loadSettings($user_id);
		try {

			$ol->saveStaticFile($file, $category, $user_id);
			
			$db->begin();	

			$ol->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			ORM::factory("Objectload", $ol->_objectload_id)->delete();
			return;
		}

		$this->json['data'] = "ok";
		$this->json['objectload_id'] = $ol->_objectload_id;
	}

	public function action_save_userstaticfile()
	{
		$category 		= $this->request->post("category");
		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			$this->json['error'] = 'Пользователь не определен';
			return;
		}
		$user_id = $user->id;

		$active_ol_count = ORM::factory("Objectload")
							->where("user_id","=", $user_id)
							->where("state","<>", 5)
							->where("state","<>", 0)
							->count_all();

		if ($active_ol_count>0)
		{
			$this->json['error'] = "У вас уже есть активные загрузки. Либо завершите загрузку, либо удалите ее.";
			return;
		}

		$file = $_FILES["file"];
		$db = Database::instance();
		if (!$category)
		{
			$this->json['error'] = 'Категория не указана';
			return;
		}

		$ol = new Objectload($user_id);
		$ol->loadSettings($user_id);
		

		try {
			$ol->saveStaticFile($file, $category, $user_id);
		} catch(Exception $e)
		{
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			ORM::factory("Objectload", $ol->_objectload_id)->_delete();
			return;
		}

		try {
			
			$db->begin();	

			$ol->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			ORM::factory("Objectload", $ol->_objectload_id)->_delete();
			return;
		}

		$ol->testFile();

		$stat = $ol->getStatistic();

		if ($stat["all"] > 0)
		{
			$allow_percent = Kohana::$config->load('massload.allow_error_percent');
			$percent = ($stat["error"]/$stat["all"])*100;
			if ($percent < $allow_percent)
				$ol->setState(1);
			else
				$ol->setState(99, "Для продолжения загрузки, процент ошибочных объявлений должен быть меньше ".$allow_percent."%. Возможно вы не настроили соответствия для справочников");
		} 
			else
		{
			$ol->setState(99, "Не обнаружено ни одной строки");
		}

		

		$this->json['data'] = "ok";
		$this->json['objectload_id'] = $ol->_objectload_id;
	}

	public function action_objectload_delete()
	{
		$this->auto_render = FALSE;
		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			throw new HTTP_Exception_404;
			return;
		}

		$post = $_POST;
		if (!$post["id"])
		{
			throw new HTTP_Exception_404;
			return;
		}

		$ct = ORM::factory('Objectload')
						->where("user_id","=",$user->id)
						->where("id","=",$post["id"])
						->where("state","IN",array(99,0,1,2,3))
						->find();

		if ( ! $ct->loaded() )
			throw new HTTP_Exception_404;

		$ct->_delete();

		$this->json['data'] = "ok";
	}

	public function action_objectload_retest()
	{
		$this->auto_render = FALSE;
		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			throw new HTTP_Exception_404;
			return;
		}
		$post = $_POST;
		if (!$post["id"])
		{
			throw new HTTP_Exception_404;
			return;
		}

		$ol = new Objectload($user->id, $post["id"]);
		$ol->testFile();
		$stat = $ol->getStatistic();

		$state = 0;

		if ($stat["all"] > 0)
		{
			$allow_percent = Kohana::$config->load('massload.allow_error_percent');
			$percent = ($stat["error"]/$stat["all"])*100;
			if ($percent < $allow_percent)
				$state = $ol->setState(1);
			else
				$state = $ol->setState(99, "Для продолжения загрузки, процент ошибочных объявлений должен быть меньше ".$allow_percent."%. Возможно вы не настроили соответствия для справочников");
		} 
			else
		{
			$state = $ol->setState(99, "Не обнаружено ни одной строки");
		}
		$this->json['data'] = "ok";
		$this->json['state'] = $state;
		$this->json['objectload_id'] = $ol->_objectload_id;
	}


	public function after()
	{
		parent::after();
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}

} 