<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Static extends Controller_Template {

	function action_index()
	{
		$this->use_layout = FALSE;
		$this->template->data = Attribute::getData();
	}

}