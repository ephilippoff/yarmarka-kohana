<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Form extends Controller_Ajax {

	public function before()
	{
		// disable global layout for this controller
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->json['code'] = 200; // code by default
	}

	public function action_upload_photofield()
	{
		$fieldname = $this->request->post("fieldname");
		try
		{			
			if (!isset($_FILES[$fieldname]) OR empty($_FILES[$fieldname]))
			{
				throw new Exception('Загружен файл не верного формата. Возможно это не картинка.');
			}

			$filename = Uploads::make_thumbnail($_FILES[$fieldname]);

			$this->json['filename'] = $filename;
			$this->json['filepaths'] = Imageci::getSitePaths($filename);
		}
			catch(Exception $e)
		{
			$this->json['error'] = $e->getMessage();
		}
	}

}