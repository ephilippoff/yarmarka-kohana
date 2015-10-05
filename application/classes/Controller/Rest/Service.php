<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_check_kupon()
	{
		$object_id = intval($this->param->id);
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
		if (!isset($this->post->key) OR !isset($this->post->serviceData["result"]) OR !isset($this->post->serviceData["info"])) {
			$this->json['code'] = 400;
			$this->json['text'] = "Ошибка при сохранении услуги. Отсутствие обязательных параметров";
			return;
		}
		
		$key = $this->post->key;
		$info = new Obj($this->post->serviceData["info"]);
		$result = new Obj($this->post->serviceData["result"]);
		
		$tempOrderItemId = ( isset($this->post->serviceData["temp_order_item_id"]) ) ? $this->post->serviceData["temp_order_item_id"] : NULL;

		$db = Database::instance();
		try {
			$db->begin();

			$objects = $info->objects;
			if ($objects) {
				$services = $info->services;
				unset($info->objects);
				unset($info->services);
				foreach ($objects as $key => $object_info) {


					$service_info = $services[$key];
					$service_name = Text::ucfirst($service_info["name"]);
					// if (in_array($service_info->service["name"], array("premium","up"))) {
					// 	$service_info->count = Service::factory($service_name)->get_balance();
					// 	$service_info->available = Service::factory($service_name)->check_available(1);
					// }
					$service = Service::factory($service_name, $object_info['id']);
					$service->set_params($result);
					$orderItemTemp	=  $service->save($object_info, $key, $tempOrderItemId);
				}
			} else {
				$service_name = Text::ucfirst($info->service["name"]);
				$service = Service::factory($service_name, $info->id);
				$service->set_params($result);
				$orderItemTemp = $service->save($info->object, $key, $tempOrderItemId);
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
