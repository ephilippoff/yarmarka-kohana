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

		$this->json['count'] = Service::factory("Up")->get_balance();
		$this->json['available'] = Service::factory("Up")->check_available(1);
		$this->json['service'] = Service::factory("Up")->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		$info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category, "city_id" => $ad->city_id));

		if ( $info["code"] == "error" )
		{
			$this->json['code'] = 400;
			$this->json['text'] = $info["errors"];
			return;
		}
		
		// if ($ad->get_service_up_timestamp() > time())
		// {
		// 	$this->json['code'] = 300;
		// 	$this->json['text'] = "Следующее бесплатное поднятие этого объявление будет доступно не ранее ". date("d.m Y H:i", $ad->get_service_up_timestamp());
		// 	return;
		// }

		// echo Debug::vars( Service::factory("Premium")
		// 	->city("tyumen")
		// 	->category("legkovye-avtomobili")
		// 	->get() );
	}

	public function action_check_premium()
	{
		$object_id = $this->post->id;
		$params = ($this->post->params) ? (array) $this->post->params : array();

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

		$service = Service::factory("Premium", $ad->id);
		$service->set_params($params);

		$this->json['count'] = Service::factory("Premium")->get_balance();
		$this->json['available'] = Service::factory("Premium")->check_available(1);
		$this->json['service'] = $service->get();
		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		$info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category, "city_id" => $ad->city_id));

		if ( $info["code"] == "error" )
		{
			$this->json['code'] = 400;
			$this->json['text'] = $info["errors"];
			return;
		}
	}

	public function action_check_lider()
	{
		$object_id = $this->post->id;
		$params = ($this->post->params) ? (array) $this->post->params : array();

		$ad = ORM::factory('Object', intval($object_id));
		if (!$ad->loaded() OR $ad->active == 0)
		{
			throw new HTTP_Exception_404;
		}

		$service = Service::factory("Lider", $ad->id);
		$service->set_params($params);

		$this->json['count'] = 0;
		$this->json['available'] = FALSE;
		$this->json['service'] = $service->get();

		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		$info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category, "city_id" => $ad->city_id));

		if ( $info["code"] == "error" )
		{
			$this->json['code'] = 400;
			$this->json['text'] = $info["errors"];
			return;
		}
	}

	public function action_check_kupon()
	{
		$object_id = intval($this->post->id);
		$ad = ORM::factory('Object', $object_id);
		if (!$ad->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$groups = ORM::factory('Kupon_Group')->get_by_object($ad->id);
		$available_count = 0;
		$this->json['groups'] = array();		foreach ($groups as $group) {
			
			$service = Service::factory("Kupon", $group->id);
			$available = $service->check_available(1);

			if ($available !== TRUE) continue;
			
			$this->json['groups'][] = array(
				"id" => $group->id,
				"available" => TRUE,
				"service" => $service->get()
			);

			$available_count = $available_count + 1;
		}

		$this->json['object'] = $ad->get_row_as_obj(array("id","title"));

		if ($available_count == 0)
		{
			$this->json['code'] = 400;
			$this->json['text'] = "Недоступен для заказа (отсутсвует)";
		}
	}

	

	public function action_save()
	{
		if (!isset($this->post->serviceData["result"]) OR !isset($this->post->serviceData["info"])) {
			$this->json['code'] = 400;
			$this->json['text'] = "Ошибка при сохранении услуги. Отсутствие обязательных параметров";
			return;
		}
		
		$service_info = new Obj($this->post->serviceData["info"]);
		$result = new Obj($this->post->serviceData["result"]);
		
		$tempOrderItemId = ( isset($this->post->serviceData["temp_order_item_id"]) ) ? $this->post->serviceData["temp_order_item_id"] : NULL;

		$db = Database::instance();
		try {
			$db->begin();

			$orderItemTemp = Service::factory(Text::ucfirst($service_info->service["name"]), $service_info->id)
							->save($service_info, $result, $tempOrderItemId);
			
			$db->commit();
		} catch (Kohana_Exception $e) {
			$db->rollback();
			$this->json["text"] = "Ошибка при сохранении услуги. ".$e->getMessage();
			$this->json["code"] = 400;
			return;
		}
		$this->json["result"] = $orderItemTemp;
	}

	public function action_cart_count()
	{
		$this->json = array_merge($this->json, Cart::get_info());
	}

	public function action_get_temp_item()
	{
		$id = (int) $this->get->id;
		$item = ORM::factory('Order_ItemTemp', $id);
		if ($item->loaded())
		{
			$item = $item->get_row_as_obj();
			$item->params = json_decode($item->params);
			$this->json["result"] = $item;
			return;
		}

		$this->json["code"] = 400;
	}
}
