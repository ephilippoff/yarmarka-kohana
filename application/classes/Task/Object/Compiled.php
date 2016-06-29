<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Compiled extends Minion_Task
{

	protected $_options = array(
		'category_id'	=> NULL,
		'active'	=> TRUE,
		'city_id'	=> NULL,
		'rewrite'	=> FALSE,
		'newspaper' => FALSE
	);

	protected function _execute(array $params)
	{
		$category_id = $params['category_id'];
		$city_id = $params['city_id'];
		$active = $params['active'];
		$rewrite = $params['rewrite'];
		$newspaper = $params['newspaper'];
		
		$object = ORM::factory('Object');
		
		if ($category_id) {
			$object = $object->where("category","=", $category_id);
		}

		if ($city_id) {
			$object = $object->where(DB::expr($city_id), "=", DB::expr("ANY(object.cities)"));
		}

		if ($active) {
			$object = $object->where("active","=", 1)->where("date_created",">=", DB::expr("NOW() - interval '6 months'"));
		}

		if ($newspaper) {
			$object = $object->where("source_id","=", 2)->where("date_created",">=", DB::expr("NOW() - interval '6 months'"));
		}

		if (!$rewrite) {
			$sub = DB::select('object_compiled_surgut.id')->from('object_compiled_surgut')
							->where("object_compiled_surgut.object_id","=",DB::expr("object.id"));

			$object->where('','NOT EXISTS',$sub);
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
		if ($item->is_newspaper_object() == 2) {
			Minion_CLI::write('title: '.$item->generate_newspaper_object_title());
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