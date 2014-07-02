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
		
		$user = Auth::instance()->get_user();
		if (empty($user))
			throw new Exception('Пользователь не определен');
		if (empty($_FILES['file']))
			throw new Exception('Не загружен файл');

		$file = $_FILES['file'];

		$ml = new Massload($file, 3, $user);
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
				throw new Exception('Файл не соответсвует требованиям, количество полей отличается (см. инструкцию по загрузке)');
				fclose($file);	
				break;
			}

			$is_error = 0;

			foreach ($item as $number=>$value) 
			{
				
				$field = $ml->get_by_key($ml->config, $number);
				$errors = $ml->check($field, $value, $str_pos);
				
			}
			
			$str_pos++;
		}

		$ml->file_close();

		if (count($errors) >0)
			$this->json['data'] = $errors;
		else {
			$this->json['pathtofile'] = $ml->pathtofile;
			$this->json['pathtoimage'] = $ml->pathtoimage;
			$this->json['data'] = 'ok';
		}


	}

	public function action_load_next_strings()
	{
		$pathtofile 	= (string) $this->response->post('pathtofile');
		$pathtoimage 	= (string) $this->response->post('pathtoimage');
		$from 			= (int) $this->response->post('from');
		$to 			= (int) $this->response->post('to');

		$user = Auth::instance()->get_user();
		$this->json['data'] = Array();

		$ml = new Massload(NULL, 3, $user);
		$ml ->pathtofile  = $pathtofile;
		$ml ->pathtoimage = $pathtoimage;
		$ml ->file_open()
			->get_config();

		$file = $ml->fileForLoop;

		$i = 0;
		while ( !feof($file) )
		{
			if ($i < $from AND $i > $to)
				continue;

			$item = fgetcsv($file, ',');

			$record = Array();

			foreach ($item as $number=>$value) 
			{
				
				$field = $ml->get_by_key($ml->config, $number);

				if ($value <> "")
					$record[$field['name']] = $value;
				
			}
			
			$record = $ml->to_post_format($record, $ml->config);

			$record['rubricid'] = $ml->category_id;

			$this->json['data'][] = Object::PlacementAds_ByMassLoad($record);

			$i++;
		}
			

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