<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kupon extends Controller_Template {

	public function before()
	{
		parent::before();
	}
	
	public function action_index()
	{
		$this->layout = 'kupon';
		$id = (int)$this->request->param('id');
		
		$user = Auth::instance()->get_user();
		
		if (!$user)
			throw new HTTP_Exception_404;
		
		$kupon = ORM::factory('Kupon', $id);
		
		if(!$kupon->loaded())
			throw new HTTP_Exception_404;
		
		if (!in_array($user->role, array(1,3)))
			if ($kupon->invoice->user_id != $user->id)
				throw new HTTP_Exception_404;				
		
		$object = ORM::factory('Object', $kupon->object_id);
		
		$attributes_values = $object->get_attributes_values($kupon->object_id, NULL, 'seotitle');
		
		$this->template->set_global('title', $object->title);
		$this->template->kupon = $kupon;
		$this->template->object = $object;
		$this->template->attributes_values = $attributes_values;
	}
}