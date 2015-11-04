<?php

class Controller_Redirect extends Controller_Template
{
	//Редирект для category_banners
	public function action_ref_cb()
	{					
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;		
		
		if (!$id = (int)$_REQUEST['id']) $this->redirect();
			
		$banner = ORM::factory('Category_Banners', $id);

		if ($banner->loaded() and !empty($banner->href)) 
		{ 
			ORM::factory('Category_Banners')->increase_visits($id);
			ORM::factory('Category_Banners_Stats')->increase_visits($id);
			$this->redirect(URL::prep_url($banner->href));
		}
		
		$this->redirect();
	}

	public function action_maitenance()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;		

		echo "Сайт в режиме обслуживания. Подождите немного и мы станем еще лучше";
	}

	public function action_old_link()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$path = $this->request->param("category_path");
		$path_segments = explode("/", $path);
		array_shift($path_segments);

		$path_detail_segments = explode("-", $path);
		if ( $object_id = (int) $path_detail_segments[count($path_detail_segments)-1] ) {
			HTTP::redirect("/detail/".$object_id, 301);
			return;
		} else {
			$query="";
			if (isset($_SERVER['QUERY_STRING'])) {
				$query = "?".$_SERVER['QUERY_STRING'];
			}
			HTTP::redirect("/".join("/",$path_segments).$query, 301);
		}
		echo Debug::vars($path);

	}
	
}
