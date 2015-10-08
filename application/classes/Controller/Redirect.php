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

	public function action_detail()
	{
		$config = Kohana::$config->load("common");
		$main_domain = $config["main_domain"];
		HTTP::redirect("http://".$main_domain."/detail/".$this->request->param("object_id"));
	}
	
}
