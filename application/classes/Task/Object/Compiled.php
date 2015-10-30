<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Compiled extends Minion_Task
{

	protected $_options = array(
		'category_id'	=> 155,
		'active'	=> TRUE,
		'city_id'	=> NULL
	);

	protected function _execute(array $params)
	{
		$category_id = $params['category_id'];
		$city_id = $params['city_id'];
		$active = $params['active'];
		
		$object = ORM::factory('Object');
		
		if ($category_id) {
			$object = $object->where("category","=", $category_id);
		}

		if ($city_id) {
			$object = $object->where("city_id","=", $city_id);
		}

		if ($active) {
			$object = $object->where("active","=", 1)->where("is_published","=", 1);
		}

		$object->order_by("date_created", "desc");
		$result = $object->find_all();

		Minion_CLI::write('Count: '.$result->count());

		foreach ($result as $item) {
			self::saveCompiled($item, TRUE);
		}
		

	}

	static function saveCompiled(ORM $item, $show_hint = FALSE)
	{
		$oc = ORM::factory('Object_Compiled')
				->where("object_id","=",$item->id)
				->find();
		$compiled = array();
		if ($oc->loaded()) {
			$compiled = unserialize($oc->compiled);
		}
		$compiled["url"] = $item->get_full_url();
		if ($show_hint)
		{
			Minion_CLI::write('url: '.$compiled["url"]);
		}
		

		$compiled["images"] = Object_Compile::getAttachments($item->id, $item->main_image_id);
		if (count($compiled["images"]) and $show_hint) {
			Minion_CLI::write('main_photo: '.($compiled["images"]["main_photo"]?"exist":""). " local:".count($compiled["images"]["local_photo"]). " remote:".count($compiled["images"]["remote_photo"]) );
		}

		$compiled = array_merge($compiled, Object_Compile::getAddress($item->location_id));
		if ($show_hint)
		{
			Minion_CLI::write('city: '.$compiled["city"].' address:'.$compiled["address"]);
		}
		$compiled = array_merge($compiled, Object_Compile::getAttributes($item));
		if ($show_hint)
		{
			Minion_CLI::write('attributes: saved');
		}

		$compiled = array_merge($compiled, Object_Compile::getAuthor($item->author_company_id, $item->author));
		if ($show_hint)
		{
			Minion_CLI::write('author: '.$compiled["author"]["email"]);
		}

		$compiled = array_merge($compiled, Object_Compile::getContacts($item->id) );
		if ($show_hint)
		{
			Minion_CLI::write('contacts: saved');
		}

		$compiled = array_merge($compiled, Object_Compile::getServices($item->id) );
		if ($show_hint)
		{
			Minion_CLI::write('services: saved');
		}

		// $compiled = array_merge($compiled, $this->getCommon($item) );
		// Minion_CLI::write('common: saved');

		
		

		$oc->object_id = $item->id;
		$oc->compiled = serialize($compiled);
		$oc->save();
	}
}