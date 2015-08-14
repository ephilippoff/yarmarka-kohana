<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kupon_Test extends Controller_Template {

	public function before()
	{
		parent::before();
	}
	
	public function action_index() 
	{
		$this->layout = 'kupon';
		$object_id = (int)$this->request->param('id');
		
		$user = Auth::instance()->get_user();
		
		if (!$user or !in_array($user->role, array(1, 3, 9)))
			throw new HTTP_Exception_404;						
		
		$object = ORM::factory('Object', $object_id);
		
		if (!$object->loaded())
			throw new HTTP_Exception_404;
		
		$attributes_values = $object->get_attributes_values($object_id, NULL, 'seotitle');
		
		$this->template->object = $object;
		$this->template->attributes_values = $attributes_values;		
		
	}
}