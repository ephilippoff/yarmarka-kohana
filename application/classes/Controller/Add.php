<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function action_index()
	{
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		if ($is_post) {  
			//echo var_dump(self::action_native_save_object());
			$params = $this->request->post();
		} 
		else
		{
			$params = array(
				'rubricid'		=> (int)$this->request->param('rubricid'),
				'object_id'		=> (int)$this->request->query('object_id'),
				'city_id'		=> (int)$this->request->param('city_id')
			);
		}

		

		$form_data = new Lib_PlacementAds_Form($params, $is_post);
		$form_data	->Category()
				 	->City()
				 	->Subject()
				 	->Text()
				 	->Photo()
				 	->Params()
				 	->Map()
				 	->Contacts();

		$this->template->params 	= $params;
		$this->template->form_data 	= $form_data->_data;

	}

	public function action_native_save_object()
	{
		$json = array();
		$user = Auth::instance()->get_user();

		//если в локале работаем с подачей, ставим 1
		$local = 0;

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

		return $json;
	}

	public function action_save_object()
	{
		$this->auto_render = FALSE;
		$json = array();
		$user = Auth::instance()->get_user();

		if ( ! Request::current()->is_ajax())
		{
			throw new HTTP_Exception_404('only ajax requests allowed');
		}

		//если в локале работаем с подачей, ставим 1
		$local = 0;

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

}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */