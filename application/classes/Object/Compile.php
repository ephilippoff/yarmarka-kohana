<?php defined('SYSPATH') or die('No direct script access.');

class Object_Compile
{
	static function saveObjectCompiled(ORM $item, $params = NULL)
	{
		$oc = ORM::factory('Object_Compiled')
				->where("object_id","=",$item->id)
				->find();
		$compiled = array();
		if ($oc->loaded()) {
			$compiled = unserialize($oc->compiled);
			$params = ($params) ? $params : $compiled;
		}
		$params = (array) $params;
		$compiled["url"] = $item->get_full_url();

		$compiled["images"] = Object_Compile::getAttachments($item->id, $item->main_image_id);
		$compiled = array_merge($compiled, Object_Compile::getAddress($item, $params));
		$compiled = array_merge($compiled, Object_Compile::getAttributes($item));
		$compiled = array_merge($compiled, Object_Compile::getAuthor($item->author_company_id, $item->author));
		$compiled = array_merge($compiled, Object_Compile::getContacts($item->id) );
		$compiled = array_merge($compiled, Object_Compile::getServices($item->id) );
		$oc->object_id = $item->id;
		$oc->compiled = serialize($compiled);
		$oc->save();
		return $compiled;
	}

	static function saveImagesToCompiled($object)
	{
		$oc = ORM::factory('Object_Compiled')
				->where("object_id","=",$object->id)
				->find();
		$compiled = array();
		if ($oc->loaded()) {
			$compiled = unserialize($oc->compiled);
		}

		$compiled["images"] = Object_Compile::getAttachments($object->id, $object->main_image_id);

		$oc->object_id = $object->id;
		$oc->compiled = serialize($compiled);
		$oc->save();
		return $compiled;
	}

	static function getAttachments($object_id, $main_photo_id) {
		$result = array();
		$result["local_photo"] 		= array();
		$result["remote_photo"] 		= array();
		$result["main_photo"] 	= NULL;
		$attachments = ORM::factory('Object_Attachment')
							->where('object_id', '=', $object_id)
							->where('type','IN', array(0, 4, 2, 3))
							->order_by('id', 'desc')
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
				$result["youtube_video"] = $attachment->filename;
			} elseif ($attachment->type == 3){
				$result["rutube_video"] = $attachment->filename;
			}
		}

		return $result;
	}

	static function getAddress($object, $params = NULL) {
		$result = array();

		$result["address"] = $object->get_address();
		$result["city"] = $object->city_obj->title;
		$result["region"] = $result["city"];

		$result["lat"] = $result["lon"] = NULL;

		$coords = $object->get_coords();

		if ($coords AND count($coords) == 2) {
			list($result["lat"],$result["lon"]) = $coords;
		}

		$result["real_city"] = @$params["real_city"];

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
		$result["services"]["cities"] = array();

		$premiums = ORM::factory('Object_Rating')
						->where("object_id","=",$object_id)
						->where("object_rating.date_expiration", ">", DB::expr("NOW()"))
						->find_all();
		foreach ($premiums as $premium) {
			$result["services"]["premium"][] = array(
				"rating" =>$premium->rating,
				"city_id" =>$premium->city_id, 
				"date_expiration" =>$premium->date_expiration,
				"count" => $premium->count,
				"activated" => $premium->activated
			);
		}

		$photocards = ORM::factory('Object_Service_Photocard')
						->where("object_id","=",$object_id)
						->where("object_service_photocard.date_expiration", ">", DB::expr("NOW()"))
						->find_all();
		foreach ($photocards as $photocard) {
			$result["services"]["lider"][] = array(
				"category_id" =>$photocard->category_id, 
				"date_expiration" =>$photocard->date_expiration,
				"count" => $photocard->count,
				"activated" => $photocard->activated
			);
		}

		$ups = ORM::factory('Object_Service_Up')
						->where("object_id","=",$object_id)
						//->where("count","<>",DB::expr("activated"))
						->find_all();
		foreach ($ups as $up) {
			$result["services"]["up"][] = array(
				"date_created" => $up->date_created,
				"count" => $up->count,
				"activated" => $up->activated
			);
		}

		$object = ORM::factory('Object', $object_id);
		if (count($object->get_cities()) > 1) {
			$result["services"]["cities"] = $object->get_cities();
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