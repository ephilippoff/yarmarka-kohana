<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Massload extends Controller_Template {

	protected $json = array();

	public function before()
	{
		// only ajax request is allowed
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		parent::before();

		$this->json['code'] = 200; // code by default
	}

	public function action_checkfile()
	{
		$category_id 	= (int) $this->request->post("category_id");
		$ignore_errors 	= (int) $this->request->post("ignore_errors");

		$user = Auth::instance()->get_user();
		try {
			$file = $_FILES['file'];
		} catch (Exception $e) {
			$this->json['critError'] = "Ошибка файла : ".$e->getMessage();
			return;
		}

		$ext = File::ext_by_mime($file['type']);
		if ( empty($ext) OR ($ext <> "csv" AND $ext <> "zip")){
			$this->json['critError'] = "Не правильный формат файла ".$ext.". Допустимые: csv, zip";
			return;
		}
		
		$critError = NULL;

		if (empty($user)) {
			$this->json['critError'] = 'Пользователь не определен';
			return;
		}
		if (empty($file)) {
			$this->json['critError'] = 'Не загружен файл';
			return;
		}
		if (!$category_id){
			$this->json['critError'] = 'Не указана категория';
			return;
		}

		$ml = new Massload($file, $category_id, $user);
		$ml ->save_input_file()
			->get_input_file_path_by_ext()
			->file_open()
			->get_config();

		$errors = Array();
		$file = $ml->fileForLoop;

		$str_pos = 0;
		while ( !feof($file) )
		{
			$item = fgetcsv($file, ',');

			if (count($item) == 1) continue;

			if ( count($item) <> count($ml->config) )
			{
				//$ml->log_error($str_pos, $field, $value, $comment)
				$critError = 'Файл не соответсвует требованиям, количество полей отличается (см. инструкцию по загрузке)';
				break;
			}

			$is_error = 0;

			foreach ($item as $number=>$value) 
			{
				
				$field = $ml->get_by_key($ml->config, $number);
				$error = $ml->check($field, $value, $str_pos);
				if (count($error) >0)
					$errors[] = $error;
				
			}
			
			$str_pos++;
		}

		$ml->file_close();

		if (count($errors) >0 OR $critError) {
			$this->json['errors'] = $errors;
			$this->json['critError'] = $critError;
		} else {
			$this->json['pathtofile'] = $ml->pathtofile;
			$this->json['pathtoimage'] = $ml->pathtoimage;
			$this->json['count'] = $str_pos;
			$this->json['data'] = 'ok';
		}


	}

	public function action_load_next_strings()
	{
		$pathtofile 	= (string) $this->request->post('pathtofile');
		$pathtoimage 	= (string) $this->request->post('pathtoimage');
		$step 			= (int) $this->request->post('step');
		$iteration 		= (int) $this->request->post('iteration');
		$category_id 	= (int) $this->request->post('category_id');
		$this->json['category_id'] = $category_id;
		if (!$category_id)
			return;

		$this->json['data'] = Array();

		$user = Auth::instance()->get_user();

		$ml = new Massload(NULL, $category_id, $user);
		$ml ->pathtofile  = $pathtofile;
		$ml ->pathtoimage = $pathtoimage;
		$ml ->file_open()
			->get_config();

		$file = $ml->fileForLoop;

		for ($i = 0; $i<$iteration*$step; $i++)
			fgetcsv($file, ',');


		while ($i<$iteration*$step+$step)
		{
			$i++;

			$record = Array();			
			$item = fgetcsv($file, ',');

			if (count($item) <= 1) 
				continue;

			foreach ($item as $number=>$value) 
			{				
				$field = $ml->get_by_key($ml->config, $number);
				if ($value <> "")
					$record[$field['name']] = $value;				
			}
			
			$record = $ml->to_post_format($record, $ml->config);

			$record['rubricid'] = $ml->category_id;

			$this->json['data'][] = Object::PlacementAds_ByMassLoad($record);

			
		}

		$ml->file_close();
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