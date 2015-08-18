<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_check_freeup()
	{
		$object_id = $this->post->id;

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

		$this->json['available'] = true;
		$this->json['service'] = Service::factory("Up")->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		$info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category));

		if ( $info["code"] == "error" )
		{
			$this->json['code'] = 400;
			$this->json['text'] = $info["errors"];
			return;
		}
		
		if ($ad->get_service_up_timestamp() > time())
		{
			$this->json['code'] = 300;
			$this->json['text'] = "Следующее бесплатное поднятие этого объявление будет доступно не ранее ". date("d.m Y H:i", $ad->get_service_up_timestamp());
			return;
		}

		// echo Debug::vars( Service::factory("Premium")
		// 	->city("tyumen")
		// 	->category("legkovye-avtomobili")
		// 	->get() );
	}

	public function action_check_premium()
	{
		$object_id = $this->post->id;

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

		$this->json['available'] = true;
		$this->json['service'] = Service::factory("Premium")->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		// $info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category));

		// if ( $info["code"] == "error" )
		// {
		// 	$this->json['code'] = 400;
		// 	$this->json['text'] = $info["errors"];
		// 	return;
		// }

	}

	public function action_check_lider()
	{
		$object_id = $this->post->id;

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

		$this->json['available'] = true;
		$this->json['service'] = Service::factory("Lider")->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		// $info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category));

		// if ( $info["code"] == "error" )
		// {
		// 	$this->json['code'] = 400;
		// 	$this->json['text'] = $info["errors"];
		// 	return;
		// }

	}

	public function action_check_buy_object()
	{
		$object_id = intval($this->post->id);
		$ad = ORM::factory('Object', $object_id);
		if (!$ad->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$service = Service::factory("Object", $object_id);

		$this->json['available'] = $service->check_available(1);
		$this->json['service'] = $service->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		if ($this->json['available'] !== TRUE)
		{
			$this->json['code'] = 400;
			$this->json['text'] = $this->json['available'];
		}
	}

	

	public function action_save()
	{
		$service_info = new Obj($this->post->serviceData["info"]);
		
		$service_result = Service::factory(Text::ucfirst($service_info->service["name"]))
						->save($service_info)
						->get_row_as_obj();

		$this->json["result"] = $service_result;
	}

	public function action_cart_count()
	{
		$this->json = array_merge($this->json, Cart::get_info());
	}
}
