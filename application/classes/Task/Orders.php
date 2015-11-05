<?php defined('SYSPATH') or die('No direct script access.');


class Task_Orders extends Minion_Task
{

	protected $_options = array(
		
	);

	protected function _execute(array $params)
	{
		Minion::write("start","orders");
		
		$orders = ORM::factory('Order')->where("state","in",array(0,1))->find_all();
		foreach ($orders as $order) {
			Minion::write(date('Y-m-d H:i:s'), $order->created);
			if (strtotime(date('Y-m-d H:i:s')) > strtotime($order->created) + 60*30) {
				ORM::factory('Order')->check_state($order->id, array(), function($order_id, $action){
					Minion::write($action, $order_id);
				});
			}
		}

		Minion::write("start","return reserved kupons");
		$this->return_reserved_kupons();

		Minion::write("start","activate services");
		$this->activate_services();
	}

	function return_reserved_kupons()
	{
		$kupons = ORM::factory('Kupon')->where("state","=","reserve")->find_all();
		foreach ($kupons as $kupon)
		{
			$operation = $kupon->get_last_operation();
			if ($operation AND $operation->loaded())
			{
				if (strtotime(date('Y-m-d H:i:s')) > strtotime($operation->date) + 60*30) {
					Minion::write($kupon->id,"Kupon reserve timeout exceeded");
					$kupon->return_to_avail("Kupon reserve timeout exceeded");
				}
			}
		}
	}

	function activate_services()
	{
		$this->activate_service_up();
		$this->activate_service_premium();
		$this->activate_service_lider();
	}

	function activate_service_up()
	{
		$services = ORM::factory('Object_Service_Up')
					->where("count","<>",DB::expr("activated"))
					->find_all();

		foreach ($services as $service) {
			if (strtotime(date('Y-m-d H:i:s')) > strtotime($service->date_created) + 60*60*24) {
				Minion::write("service_up");
				$service->date_created = date('Y-m-d H:i:s');
				$service->save();
				Service_Up::apply_service($service->object_id, 1);
				Service::saveServiceInfoToCompiled($service->object_id);
			}
		}
	}

	function activate_service_premium()
	{
		$services = ORM::factory('Object_Rating')
					->where("date_expiration", "<", DB::expr("NOW()"))
					->where("count","<>",DB::expr("activated"))
					->find_all();

		foreach ($services as $service) {
			if (strtotime(date('Y-m-d H:i:s')) > strtotime($service->date_expiration)) {
				Minion::write("service_premium");
				// $service->date_expiration = date('Y-m-d H:i:s', strtotime($service->date_expiration) + 60*60*24*7);
				// $service->save();
				Service_Premium::apply_service($service->object_id, 1);
				Service::saveServiceInfoToCompiled($service->object_id);
			}
		}
	}

	function activate_service_lider()
	{
		$services = ORM::factory('Object_Service_Photocard')
					->where("date_expiration", "<", DB::expr("NOW()"))
					->where("count","<>",DB::expr("activated"))
					->where("active","=",1)
					->find_all();

		foreach ($services as $service) {
			if (strtotime(date('Y-m-d H:i:s')) > strtotime($service->date_expiration)) {
				Minion::write("service_lider", $service->id);
				// $service->date_expiration = date('Y-m-d H:i:s', strtotime($service->date_expiration) + 60*60*24*7);
				// $service->save();

				$_cities = array_map('intval', explode(",", trim(trim($service->cities,"{"),"}")));
				$cities = array_map(function($item) {
					return $item->seo_name;
				} , (array) ORM::factory('City')->where("id","IN", $_cities)->getprepared_all());

				$_categories = array_map('intval', explode(",", trim(trim($service->categories,"{"),"}")));
				$categories = array_map(function($item) {
					return $item->seo_name;
				} , (array) ORM::factory('Category')->where("id","IN", $_categories)->getprepared_all());

				Service_Lider::apply_service($service->object_id, 1, $cities, $categories);
				Service::saveServiceInfoToCompiled($service->object_id);
			}
		}
	}

}