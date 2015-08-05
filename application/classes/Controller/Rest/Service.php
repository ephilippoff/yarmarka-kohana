<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_check_freeup()
	{
		$object_id = $this->post->id;
		$is_check = $this->post->is_check;

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

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


	public function action_save()
	{
		$service_info = new Obj($this->post->serviceData["info"]);
		$service_params = new Obj(isset($this->post->serviceData["params"]) ? $this->post->serviceData["params"] : array() ) ;

		// $service = ORM::factory('Services')
		// 				->where("name","=",$service_name)
		// 				->find();

		$service = Service::factory(Text::ucfirst($service_info->service["name"]));
		if ($service_info->category) {
			$service = $service->category($service_info->category);
		}
		if ($service_info->city) {
			$service = $service->category($service_info->city);
		}

		$service_info->service = $service->get();

		$service_params->total = $service->calculate_total((array) $service_params);

		$total_params = json_encode(array_merge((array) $service_params, (array) $service_info));

		$key = Cart::get_key();

		$order_item_temp = ORM::factory('Order_ItemTemp');

		$order_item_temp->object_id = $service_info->object["id"];
		$order_item_temp->service_id = NULL;
		$order_item_temp->params = $total_params;
		$order_item_temp->key = $key;
		$order_item_temp->save();

		$this->json["result"] = $order_item_temp->get_row_as_obj();
	}
}
