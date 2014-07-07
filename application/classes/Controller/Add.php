<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function action_save_object()
	{
		
		$this->auto_render = FALSE;
		$json = array();
		$user = Auth::instance()->get_user();

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
		$local = 1;

		if ($local == 1)
		{
			//подставляется дефолтный город 1947 не из кладра, чтоб кладр на локале не разворачивать
			//не сохраняется короткий урл
			$json = Object::PlacementAds_Local($this->request->post());

		} else {
			if ($user->role == 9) 
			{
				//убрана проверка контактов
				//убрана проверка на максимальное количество объяв в рубрику
				$json = Object::PlacementAds_ByModerator($this->request->post());
			} else {
				$json = Object::PlacementAds_Default($this->request->post());
			}
		}

		$this->response->body(json_encode($json));
	}

	public function action_massload()
	{
		$this->auto_render = FALSE;
		
		$user = Auth::instance()->get_user();
		if (empty($user))
			throw new Exception('Пользователь не определен');

		$file = $_FILES['file'];

		$ml = new Massload($file, 3, $user);
		$ml ->save_input_file()
			->get_input_file_path_by_ext()
			->file_open()
			->get_config();
		
		$errors = Array();
		$file = $ml->fileForLoop;

		if ($file)
		{
			$str_pos = 0;
			while ( !feof($file) )
			{
				$item = fgetcsv($file, ',');

				if (count($item) == 1) continue;

				if ( count($item) <> count($ml->config) )
				{
					throw new Exception('Количество полей не совпадает');
					fclose($file);	
					break;
				}

				$is_error = 0;
				$record = Array();

				foreach ($item as $number=>$value) 
				{
					
					$field = $ml->get_by_key($ml->config, $number);

					$errors = $ml->check($field, $value, $str_pos);

					if (count($errors) > 0)
						$is_error = 1;

					if ($value <> "")
						$record[$field['name']] = $value;
					
				}

				if ($is_error == 0)
				{

					$record = $ml->to_post_format($record, $ml->config);

					$record['rubricid'] = $ml->category_id;

					$json = Object::PlacementAds_ByMassLoad($record);
					echo var_dump($json);
				}

				$str_pos++;
			}
			echo var_dump($errors);
		} else {
			throw new Exception('Ошибка при открытии файла');
			fclose($file);
		}
		fclose($file);


	}

}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */