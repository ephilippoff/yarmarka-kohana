<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_check_freeup()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->post->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$info = Object::canEdit(Array("object_id" => $object->id, "rubricid" => $object->category, "city_id" => $object->city_id));
			if ( $info["code"] == "error" )
			{
				$this->json['code'] = 400;
				$this->json['text'] = $info["errors"];
				$errors = $errors + 1;
				continue;
			}
			$objects_to_action[] = $object->get_row_as_obj(array("id","title"));
			$services_to_action[] = Service::factory("Up")->get();
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}

		$this->json['count'] = Service::factory("Up")->get_balance();
		$this->json['available'] = Service::factory("Up")->check_available(1);
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;

		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
		}
	}

	public function action_check_premium()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->post->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->post->params) ? (array) $this->post->params : array();

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$info = Object::canEdit(Array("object_id" => $object->id, "rubricid" => $object->category, "city_id" => $object->city_id));
			if ( $info["code"] == "error" )
			{
				$this->json['code'] = 400;
				$this->json['text'] = $info["errors"];
				$errors = $errors + 1;
				continue;
			}
			$object =  $object->get_row_as_obj(array("id","title"));
			$objects_to_action[] = $object;

			$service = Service::factory("Premium", $object->id);
			$service->set_params($params);
			$services_to_action[] = $service->get();
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}

		$this->json['count'] = Service::factory("Premium")->get_balance();
		$this->json['available'] = Service::factory("Premium")->check_available(1);
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;

		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
		}
	}

	public function action_check_lider()
	{
		$ids = ($this->post->ids) ? $this->post->ids: array($this->post->id);

		if (!$ids OR !count($ids)) {
			throw new HTTP_Exception_404;
		}

		$params = ($this->post->params) ? (array) $this->post->params : array();

		$objects = ORM::factory('Object')->where("id","IN", $ids)->where("active","=",1)->find_all();
		$objects_to_action = array();
		$services_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$info = Object::canEdit(Array("object_id" => $object->id, "rubricid" => $object->category, "city_id" => $object->city_id));
			if ( $info["code"] == "error" )
			{
				$this->json['code'] = 400;
				$this->json['text'] = $info["errors"];
				$errors = $errors + 1;
				continue;
			}
			$object =  $object->get_row_as_obj(array("id","title"));
			$objects_to_action[] = $object;

			$service = Service::factory("Lider", $object->id);
			$service->set_params($params);
			$services_to_action[] = $service->get();
		}

		if ($errors > 0){
			$this->json['text'] = "В выбранных объявлениях присуствуют ошибки. ". $this->json['text'];
		}

		if (count($objects_to_action) == 0 OR $this->json['code'] <> 200) {
			$this->json['code'] = 400;
		}

		$this->json['count'] = 0;
		$this->json['available'] = FALSE;
		$this->json['services'] = $services_to_action;
		$this->json['objects'] = $objects_to_action;
		if (count($objects_to_action) == 1) {
			$this->json['object'] = $objects_to_action[0];
			$this->json['service'] = $services_to_action[0];
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
		$this->json['groups'] = array();		

		foreach ($groups as $group) {
			
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

			$objects = $service_info->objects;
			if ($objects) {
				$services = $service_info->services;
				unset($service_info->objects);
				unset($service_info->services);
				foreach ($objects as $key => $object_info) {

					$service_info->object = $object_info;
					$service_info->service = $services[$key];
					$service_name = Text::ucfirst($service_info->service["name"]);
					if (in_array($service_info->service["name"], array("premium","up"))) {
						$service_info->count = Service::factory($service_name)->get_balance();
						$service_info->available = Service::factory($service_name)->check_available(1);
					}
					$orderItemTemp = Service::factory($service_name, $service_info->object['id'])
									->save($service_info, $result, $tempOrderItemId);
				}
			} else {
				$service_name = Text::ucfirst($service_info->service["name"]);
				$orderItemTemp = Service::factory($service_name, $service_info->id)
								->save($service_info, $result, $tempOrderItemId);
			}

			
			
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
