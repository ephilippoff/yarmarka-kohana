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
	
}
