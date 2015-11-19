<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kupon extends Controller_Template {

	public function before()
	{
		parent::before();
		//$this->use_layout = FALSE;
		//$this->auto_render = FALSE;

		$this->domain = new Domain();
		if ($proper_domain = $this->domain->is_domain_incorrect()) {
			//HTTP::redirect("http://".$proper_domain, 301);
		}
	}
	
	public function action_print()
	{
		$this->layout = "kupon";
		$twig = Twig::factory('detail/kupon/print');

		$id = (int) $this->request->param('id');
		$key = $this->request->query('key');
		
		
		$kupon = ORM::factory('Kupon', $id);
		$kupon_group = ORM::factory('Kupon_Group', $kupon->kupon_group_id);
		
		if(!$kupon->loaded())
			throw new HTTP_Exception_404;
		
		if (!Acl::check_kupon($kupon, "kupon", $key))
			throw new HTTP_Exception_404;
		
		$cities = array("1919" => "tyumen","1948" => "nizhnevartovsk", "1979" => "surgut");

		$twig->kupon = $kupon;
		$twig->kupon_number = Text::format_kupon_number($kupon->decrypt_number($twig->kupon->number));
		$twig->kupon_group = $kupon_group;

		$object = ORM::factory('Object', $kupon_group->object_id);
		if ($object->loaded())
		{
			$location = explode(",", $object->geo_loc);
			$twig->location = implode(",", array($location[1],$location[0]));
			$twig->city = Arr::get($cities, $object->city_id, NULL);
		}

		$ean = empty($twig->kupon->external_number) ? $twig->kupon->number : $twig->kupon->external_number;
		//debug
		//$ean = '123';
		//$ean = '123456789012345678';
		//debug done
		$eanDiff = 13 - strlen($ean);
		if ($eanDiff < 0) {
			$ean = substr($ean, abs($eanDiff));
		} else if ($eanDiff > 0) {
			$ean = str_repeat('0', $eanDiff) . $ean;
		}

		$twig->ean = $ean;
		$this->template->twig_template = $twig;
		$this->template->set_global('title', $twig->kupon_group->title);
	}

	public function action_check()
	{
		$this->layout = "cart";
		$twig = Twig::factory('detail/kupon/check');

		$this->template->twig_template = $twig;
		$this->template->set_global('title', "Проверка номера купона");
	}
}