<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kupon extends Controller_Template {

	public function before()
	{
		parent::before();
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->domain = new Domain();
		if ($proper_domain = $this->domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}
	}
	
	public function action_print()
	{
		$twig = Twig::factory('detail/kupon/print');

		$id = (int)$this->request->param('id');
		$key = (int)$this->request->query('key');
		
		
		$kupon = ORM::factory('Kupon', $id);
		$kupon_group = ORM::factory('Kupon_Group', $kupon->kupon_group_id);
		
		if(!$kupon->loaded())
			throw new HTTP_Exception_404;
		
		if (!Acl::check_kupon($kupon, "kupon", $key))
			throw new HTTP_Exception_404;
		
		$twig->kupon = $kupon;
		$twig->kupon_number = $kupon->decrypt_number($twig->kupon->number);
		$twig->kupon_group = $kupon_group;

		$object = ORM::factory('Object', $kupon_group->object_id);
		if ($object->loaded())
		{
			$location = explode(",", $object->geo_loc);
			$twig->location = implode(",", array($location[1],$location[0]));
		}

		$this->response->body($twig);
	}
}