<?php defined('SYSPATH') or die('No direct script access.');

class Form_Custom_Userinfo extends Form_Custom_Orginfo {

	public $user = NULL;
	public $_settings = NULL;

	function __construct()
	{
		$this->prefix = "userinfo-";
		$this->_settings = Kohana::$config->load("form/custom.userinfo");
		$this->user = Auth::instance()->get_user();
	}
}