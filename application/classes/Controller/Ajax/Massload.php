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

	public function action_save_userpricefile()
	{
		$title 		= $this->request->post("title");
		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			$this->json['error'] = 'Пользователь не определен';
			return;
		}
		$user_id = $user->id;
		$file = $_FILES["file"];

		$priceload = ORM::factory("Priceload");
		$limit = 0;
		$free_limit = Kohana::$config->load('priceload.free_limit');

		$setting_limit = ORM::factory('User_Settings')
    							->get_by_name($user_id, "priceload_limit")
    							->find();
    	if ($setting_limit->loaded())
    		$limit = $setting_limit->value*1;

		$quantity_price = $priceload->where("user_id","=",$user_id)
								->count_all();

		if ($limit AND $quantity_price >= $limit)
		{
			$this->json['data'] ="error";
			$this->json['error'] = "Вы оплатили загрузку до ".$limit." прайс-листов. Свяжитесь с нами, чтобы увеличить лимит.";
			return;
		} elseif (!$limit AND $free_limit AND $quantity_price >= $free_limit)
		{
			$this->json['data'] ="error";
			$this->json['error'] = "Бесплатно можно загрузить ".$free_limit." прайс-лист. Свяжитесь с нами, чтобы увеличить лимит.";
			return;
		}

		$db = Database::instance();

		$settings = new Obj();
		$settings->file = $file;
		$settings->title = $title;

		$pl = new Priceload($user_id, $settings);		

		/*try {
			
			$db->begin();	

			$pl->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = "Непредвиденная ошибка при загрузке (saveTempRecordsByLoadedFiles). Возможно файл содержит некорректные строки";
			Log::instance()->add(Log::NOTICE, $e->getMessage());
			ORM::factory("Priceload", $pl->_priceload_id)->delete();
			return;
		}*/

		$pl->setState(1);
		


		$this->json['data'] = "ok";
		$this->json['priceload_id'] = $pl->_priceload_id;
	}

	public function action_priceload_delete()
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

		if ($user->role ==1 OR $user->role ==9)
			$ct = ORM::factory('Priceload')
						->where("id","=",$post["id"])
						->find();
		else
			$ct = ORM::factory('Priceload')
						->where("user_id","=",$user->id)
						->where("id","=",$post["id"])
						->where("state","IN",array(99,0,1,2,3))
						->find();

		if ( ! $ct->loaded() )
			throw new HTTP_Exception_404;

		$ct->_delete();

		$this->json['data'] = "ok";
	}

	public function action_pricerow_loadimage()
	{
		$price_id = $this->request->post("price_id");
		$pricerow_id = $this->request->post("pricerow_id");

		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			throw new HTTP_Exception_404;
			return;
		}

		$file = $_FILES["file"];

		if ($user->role ==1 OR $user->role ==9)
			$price = ORM::factory('Priceload')
						->where("id","=",$price_id)
						->find();
		else
			$price = ORM::factory('Priceload')
						->where("user_id","=",$user->id)
						->where("id","=",$price_id)
						->find();

		if (!$price->loaded())
		{
			$this->json['error'] = "Прайс не обнаружен";
			return;
		}

		$pricerow =  ORM_Temp::factory($price->table_name, $pricerow_id);
		if (!$pricerow->loaded())
		{
			$this->json['error'] = "Строка не обнаружена";
			return;
		}

		try
		{
			$filename = Uploads::make_thumbnail($file);
			$filepaths = Imageci::getSitePaths($filename);
			$filepath = $filepaths["120x90"];

		}
		catch(Exception $e)
		{
			$this->json['error'] = $e->getMessage();
			return;
		}

		$pricerow->image = $filename;
		$pricerow->save();

		$this->json['filepath'] = $filepath;
		$this->json['data'] = "ok";
	}

	public function action_priceload_toindex()
	{

		$priceload_id = $this->request->post("priceload_id");

		$user = Auth::instance()->get_user();
		if (!$user->loaded() OR ($user->role <>1 AND $user->role<>9) OR !$priceload_id)
		{
			throw new HTTP_Exception_404;
			return;
		}

		$user_id = $user->id;

		$file = $_FILES["file"];
		$db = Database::instance();
		if (!$user_id)
			return;

		$settings = new Obj();

		$f = new Massload_File();
		@list($filepath, $imagepath) = $f->init($file, $user_id);

		$pl = new Priceload($user_id, $settings, $priceload_id);
		
		$pl->_filepath =  $filepath;

		$load = ORM::factory('Priceload', $priceload_id);
		
		try {
			Temptable::delete_table($load->table_name);
		} catch (Exception $e)
		{
			return;
		}

		$load->filepath = $filepath;
		$load->config = NULL;
		$load->save();

		try {
			
			$db->begin();	

			$pl->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			return;
		}

		$pl->setState(2);

		$this->json['data'] = "ok";
		$this->json['priceload_id'] = $pl->_priceload_id;

	}

	public function action_priceload_selftoindex()
	{
		$priceload_id = $this->request->post("id");

		$user = Auth::instance()->get_user();
		if (!$user->loaded() OR ($user->role <>1 AND $user->role<>9) OR !$priceload_id)
		{
			throw new HTTP_Exception_404;
			return;
		}

		$user_id = $user->id;

		
		$db = Database::instance();
		if (!$user_id)
			return;

		$settings = new Obj();


		$pl = new Priceload($user_id, $settings, $priceload_id);

		

		$load = ORM::factory('Priceload', $priceload_id);

		try {
			Temptable::delete_table($load->table_name);
		} catch (Exception $e)
		{
			return;
		}
		$load->filepath = $load->filepath_original;
		$load->config = NULL;
		$load->save();

		$pl->_filepath =  $load->filepath_original;

		try {
			
			$db->begin();	

			$pl->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			return;
		}

		$pl->setState(2);

		$this->json['data'] = "ok";
		$this->json['priceload_id'] = $pl->_priceload_id;

	}

	public function action_pricerow_delete()
	{
		$price_id = $this->request->post("price_id");
		$pricerow_id = $this->request->post("pricerow_id");

		$user = Auth::instance()->get_user();
		if (!$user->loaded())
		{
			throw new HTTP_Exception_404;
			return;
		}

		if ($user->role ==1 OR $user->role ==9)
			$price = ORM::factory('Priceload')
						->where("id","=",$price_id)
						->find();
		else
			$price = ORM::factory('Priceload')
						->where("user_id","=",$user->id)
						->where("id","=",$price_id)
						->find();

		if (!$price->loaded())
		{
			$this->json['error'] = "Прайс не обнаружен";
			return;
		}

		$pricerow =  ORM_Temp::factory($price->table_name, $pricerow_id);
		if (!$pricerow->loaded())
		{
			$this->json['error'] = "Строка не обнаружена";
			return;
		}

		$pricerow->delete();

		$this->json['data'] = "ok";
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
			$this->json['error'] = "Непредвиденная ошибка при загрузке (saveStaticFile). Возможно файл содержит некорректные строки";
			Log::instance()->add(Log::NOTICE, $e->getMessage());
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
			$this->json['error'] = "Непредвиденная ошибка при загрузке (saveTempRecordsByLoadedFiles). Возможно файл содержит некорректные строки";
			Log::instance()->add(Log::NOTICE, $e->getMessage());
			ORM::factory("Objectload", $ol->_objectload_id)->_delete();
			return;
		}

		$ol->testFile();

		$stat = $ol->getStatistic();

		$free_limit = $limit = Kohana::$config->load('massload.free_limit');

		$setting_limit = ORM::factory('User_Settings')
    							->get_by_name($user_id, "massload_limit")
    							->find();
    	if ($setting_limit->loaded())
    		$limit = (int) $setting_limit->value;

		if ($stat["all"] > $limit)
		{
			$this->json['data'] ="error";
			if ($setting_limit->loaded())
				$this->json['error'] = "Количество объявлений в файле превышает оплаченный лимит. Максумум ".$limit." объявлений. Свяжитесь с нами, чтобы увеличить лимит.";
			else
				$this->json['error'] = "Бесплатная загрузка ограничена. Максумум ".$free_limit." объявлений. Свяжитесь с нами, чтобы увеличить лимит.";
			ORM::factory("Objectload", $ol->_objectload_id)->_delete();
			return;
		}

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

		if ($user->role ==1 OR $user->role ==9)
			$ct = ORM::factory('Objectload')
						->where("id","=",$post["id"])
						->find();
		else
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

	public function action_save_pricefile()
	{
		$user_id 		= $this->request->post("user_id");
		$title 		= $this->request->post("title");
		$file = $_FILES["file"];
		$db = Database::instance();
		if (!$user_id)
			return;

		$settings = new Obj();
		$settings->file = $file;
		$settings->title = $title;

		$ol = new Priceload($user_id, $settings);
		

		try {
			
			$db->begin();	

			$ol->saveTempRecordsByLoadedFiles();

			$db->commit();

		} catch(Exception $e)
		{
			$db->rollback();
			$this->json['data'] ="error";
			$this->json['error'] = $e->getMessage();
			ORM::factory("Priceload", $ol->_priceload_id)->delete();
			return;
		}

		$ol->setState(2);

		$this->json['data'] = "ok";
		$this->json['priceload_id'] = $ol->_priceload_id;
	}

	public function action_priceload_create_filters()
	{
		$priceload_id = $this->request->post("id");

		$priceload = ORM::factory('Priceload',$priceload_id);

		$config = new Obj(unserialize($priceload->config));

		$fields = Priceload::getFieldsFromConfig($config, "filter");

		$filters = array();
		foreach ($fields as $field_key => $field_value) {
			$pa = ORM::factory('Priceload_Attribute')
					->where("priceload_id","=",$priceload_id)
					->where("column","=", $field_key)
					->find();

			$pa->priceload_id = $priceload_id;
			$pa->title = $field_value["title"];
			$pa->column = $field_key;
			$pa->save();

			$_filters = DB::select($field_key, DB::expr("count(".$field_key.")"))->from("_temp_".$priceload->table_name)
		 				->group_by($field_key)
		 				->order_by($field_key,"asc")
		 				->execute()->as_array($field_key);
		 	foreach ($_filters as  $filter) {
		 		$filter["column"] = $field_key;
		 		$filter["title"] = $filter[$field_key];
		 		$filter["priceload_attribute"] = $pa->id;
		 		$filters[] = $filter;
		 	}
		}

		foreach ($filters as $filter) {
			if ($filter["title"] == "" OR !$filter["title"])
				continue;

			$_filtered_rows = DB::select("id")->from("_temp_".$priceload->table_name)
							->where($filter["column"],"=",$filter["title"])							
			 				->execute()->as_array("id");
			$filtered_rows = serialize(array_keys($_filtered_rows));

			$pf = ORM::factory('Priceload_Filter')
					->where("priceload_id","=",$priceload_id)
					->where("column","=",$filter["column"])
					->where("title","=",$filter["title"])
					->find();
			$pf->title = $filter["title"];
			$pf->column = $filter["column"];
			$pf->priceload_id = $priceload_id;
			$pf->count = $filter["count"];
			$pf->filtered_rows = $filtered_rows;
			$pf->priceload_attribute_id = $filter["priceload_attribute"];
			$pf->save();
		}		

		echo Debug::vars($filtered_rows);

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