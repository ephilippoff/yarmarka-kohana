<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Controller_Template {

	public function before()
	{
		parent::before();
		$this->layout = "landing";
		
	}
	

	function action_index()
	{
		$this->template->title = "Здесь будет страница ".$_SERVER['HTTP_FROM'].".ya24.biz";
	}

	function action_show()
	{

		$this->template->title =  "Здесь будет страница ".$_SERVER['HTTP_FROM'].".ya24.biz/show";

	}
} // End Welcome
