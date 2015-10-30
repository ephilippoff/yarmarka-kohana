<?php defined('SYSPATH') or die('No direct script access.');

class Detailpage_Landing extends Detailpage_Default
{
	protected $search_info = NULL;

	public function __construct($object)
	{
		parent::__construct($object);
	}

	public function get_landing_info()
	{
		$object = $this->_orm_object;
		$info = array();
		
		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

}