<?php defined('SYSPATH') or die('No direct script access.');

class Task_Object_Compiled extends Minion_Task
{

	protected $_options = array(
		'category_id'	=> 96,
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
		

		$compiled["images"] = self::getAttachments($item->id, $item->main_image_id);
		if (count($compiled["images"]) and $show_hint) {
			Minion_CLI::write('main_photo: '.($compiled["images"]["main_photo"]?"exist":""). " local:".count($compiled["images"]["local_photo"]). " remote:".count($compiled["images"]["remote_photo"]) );
		}

		$compiled = array_merge($compiled, self::getAddress($item->location_id));
		if ($show_hint)
		{
			Minion_CLI::write('city: '.$compiled["city"].' address:'.$compiled["address"]);
		}
		$compiled = array_merge($compiled, self::getAttributes($item));
		if ($show_hint)
		{
			Minion_CLI::write('attributes: saved');
		}

		$compiled = array_merge($compiled, self::getAuthor($item->author_company_id, $item->author));
		if ($show_hint)
		{
			Minion_CLI::write('author: '.$compiled["author"]["email"]);
		}

		$compiled = array_merge($compiled, self::getContacts($item->id) );
		if ($show_hint)
		{
			Minion_CLI::write('contacts: saved');
		}

		$compiled = array_merge($compiled, self::getServices($item->id) );
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

	static function getAttachments($object_id, $main_photo_id) {
		$result = array();
		$result["local_photo"] 		= array();
		$result["remote_photo"] 		= array();
		$result["main_photo"] 	= NULL;
		$attachments = ORM::factory('Object_Attachment')
							->where('object_id', '=', $object_id)
							->where('type','IN', array(0, 4))
							->find_all();

		foreach ($attachments as $attachment) {
			if ($attachment->type == 0) {
				if ($attachment->id == $main_photo_id)
				{
					$result["main_photo"] = $attachment->filename;
				}

				$result["local_photo"][] = $attachment->filename;
			} elseif ($attachment->type == 4){
				$result["remote_photo"][] = $attachment->filename;
			} elseif ($attachment->type == 2){
				$result["youtube_video"][] = $attachment->filename;
			} elseif ($attachment->type == 3){
				$result["rutube_video"][] = $attachment->filename;
			}
		}

		return $result;
	}

	static function getAddress($location_id) {
		$result = array();
		$result["address"] = NULL;
		$result["city"] = NULL;
		$result["region"] = NULL;
		$result["lat"] = NULL;
		$result["lon"] = NULL;

		$location = ORM::factory('Location')
					->where('id', '=', $location_id)
					->find();

		$result["address"] = $location->address;
		$result["city"] = $location->city;
		$result["region"] = $location->region;
		$result["lat"] = $location->lat;
		$result["lon"] = $location->lon;
		return $result;
	}

	static function getAttributes($object) {
		$result = array();
		$result["attributes"] 	= array();

		foreach (array("list","integer","numeric","text") as $item) {
			$data = ORM::factory('Data_'.Text::ucfirst($item))
						->join('reference')
							   ->on("data_".$item.".reference","=","reference.id")
						->where("reference.category", "=", (int) $object->category )
						->where("object","=",$object->id)
						->order_by("reference.weight")
						->find_all();
			foreach ($data as $data_item) {
				$result["attributes"][] = $data_item->get_compile();
			}
		}

		return $result;
	}

	static function getAuthor($user_id, $real_author_id) {
		$result = array();

		$result["real_author_id"] 	= $real_author_id;
		$result["author"] = ORM::factory('User', $user_id)->get_compile();

		return $result;
	}

	static function getContacts($object_id) {
		$result = array();
		$result["contacts"] = array();

		$contacts = ORM::factory('Object_Contact')
						->where("object_id","=",$object_id)
						->find_all();
		foreach ($contacts as $contact) {
			$result["contacts"][] = array("type" => $contact->contact_obj->contact_type_id, "value" => $contact->contact_obj->contact_clear);
		}

		return $result;
	}

	static function getServices($object_id) {
		$result = array();
		$result["services"] = array();
		$result["services"]["premium"] = array();
		$result["services"]["lider"] = array();

		$premiums = ORM::factory('Object_Rating')
						->where("object_id","=",$object_id)
						->where("object_rating.date_expiration", ">", DB::expr("NOW()"))
						->find_all();
		foreach ($premiums as $premium) {
			$result["services"]["premium"][] = array(
				"rating" =>$premium->rating,
				"city_id" =>$premium->city_id, 
				"date_expiration" =>$premium->date_expiration
			);
		}

		$photocards = ORM::factory('Object_Service_Photocard')
						->where("object_id","=",$object_id)
						->where("object_service_photocard.date_expiration", ">", DB::expr("NOW()"))
						->find_all();
		foreach ($photocards as $photocard) {
			$result["services"]["lider"][] = array(
				"category_id" =>$photocard->category_id, 
				"date_expiration" =>$photocard->date_expiration
			);
		}

		$photocards = ORM::factory('Object_Service_Up')
						->where("object_id","=",$object_id)
						->where("object_service_up.date_created", ">", DB::expr("NOW() - interval '7 days'"))
						->find_all();
		foreach ($photocards as $photocard) {
			$result["services"]["up"][] = array(
				"date_created" => $photocard->date_created
			);
		}

		return $result;
	}

	static function getCommon($object) {
		$result = array();

		$category = ORM::factory('Category', $object->category)->find();
		$result["category"] = $category->get_row_as_obj();
		

		return $result;
	}
}