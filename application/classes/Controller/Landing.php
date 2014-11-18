<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Controller_Template {

	public function before()
	{
		parent::before();

		$this->layout = "landing";

		if (array_key_exists("HTTP_FROM", $_SERVER))
			$this->domain = str_replace(".ya24.biz", "", $_SERVER["HTTP_FROM"]);
		else
			$this->domain = $this->request->param("domain");
				
	}
	

	function action_index()
	{
		$this->template->title = "Здесь будет страница ".$this->domain;
	}

	function action_show()
	{

		$this->template->title =  "Здесь будет страница ".$this->domain."/show";

	}
} // End Welcome
