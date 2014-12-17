<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Landing extends Controller_Template {

	private $object = FALSE;

	public function before()
	{
		parent::before();

		$this->layout = "landing";

		if (array_key_exists("HTTP_FROM", $_SERVER))
			$this->domain = str_replace(".ya24.biz", "", $_SERVER["HTTP_FROM"]);
		else
			$this->domain = $this->request->param("domain");

		$this->landing = ORM::factory('Landing')
							->where_cached("domain", "=",$this->domain,0)
							->find();

		if (!$this->landing->loaded())
			throw new HTTP_Exception_404;

		if ($this->landing->object_id)
		{
			$this->object = ORM::factory('Object')
								->where_cached("id","=",$this->landing->object_id,Date::DAY)
								->find();

			if (!$this->object->loaded())
				throw new HTTP_Exception_404;
		}
		elseif ($this->landing->user_id)
		{
			$this->user = ORM::factory('User')
								->where_cached("id","=",$this->landing->user_id,Date::DAY)
								->find();
		}
				
	}
	

	function action_index()
	{
//		$this->use_layout = FALSE;
//		$this->auto_render = FALSE;

		$this->assets->js("landing.js");

		if ($this->object)
		{
			$lo = new Landing_Object($this->object);
//			echo Debug::vars($lo->user);
//			echo Debug::vars($lo->attributes);
//			echo Debug::vars($lo->contacts);
//			echo Debug::vars($lo->images);
//			echo Debug::vars($lo->location);
//			echo Debug::vars($lo->pricerows);
//			echo Debug::vars($lo->favorite);
		}
		
		$this->template->set_global('data', $lo);
	}

	function action_show()
	{

		$this->template->title =  "Здесь будет страница ".$this->domain."/show";

	}
} // End Welcome
