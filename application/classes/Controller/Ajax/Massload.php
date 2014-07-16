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
		$ignore_errors 	= (int) $this->request->post("ignore_errors");

		$user = Auth::instance()->get_user();
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
		$step 			= (int) $this->request->post('step');
		$iteration 		= (int) $this->request->post('iteration');
		$category_id 	= $this->request->post('category_id');

		$this->json['category_id'] = $category_id;

		if (!$category_id)
			return;

		$user = Auth::instance()->get_user();

		$ml = new Massload();

		$this->json['data'] = $ml->saveStrings($pathtofile, $pathtoimage, $category_id, $step, $iteration);
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