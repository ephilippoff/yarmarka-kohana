<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddUnion extends Lib_PlacementAds_AddEdit {

	function Lib_PlacementAds_AddUnion($objects_for_union){
		$this->init_defaults();
		$this->objects_for_union = $objects_for_union;
		$this->object_source_id  = $objects_for_union["current_object_source"];
	}

	function init_instances()
	{
		parent::init_instances();

		if ($this->object_source_id) {
			$this->object_source = ORM::factory('Object', $this->object_source_id);
			if (!$this->object_source->loaded())
			{
				$this->raise_error('object_source_id not finded 1');
			}
		} else
			$this->raise_error('object_source_id not finded 2');

		return $this;
	}

	function prepare_object()
	{
		$object = 	&$this->object;
		$params = 	&$this->params;
		$city = 	&$this->city;
		$category = &$this->category;
		$object_source = &$this->object_source;

		$object->category 			= $category->id;
		
		$object->ip_addr 			= Request::$client_ip;
		$object->active 			= 1;
		$object->is_published 		= 1;
		$object->author 			= NULL;
		$object->geo_loc 			= NULL;
		
		if ($object_source->location_id)
		{
			$object->location_id = $object_source->location_id;
			$object->city_id	 = $object_source->city_id;
		}

		return $this;
	}

	function copy_photo()
	{
		$params = &$this->params;
		$object = &$this->object;
		$objects_for_union = &$this->objects_for_union;

		$exist_photo = Array();
		$data = ORM::factory('Object_Attachment')->where('object_id', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_photo[] = $item->signature;

		$oa = ORM::factory('Object_Attachment')
				->join('object', 'left')
						->on('object.id', '=', 'object_id')
				->where("object.active","=",1)
				->where("object.is_published","=",1)
				->where('object_id', 'IN', array_values($objects_for_union))->find_all();
		// собираем аттачи
		foreach ($oa as $item)
		{
			if ( !in_array($item->signature, $exist_photo) )
			{
				$attachment = ORM::factory('Object_Attachment');
				$attachment->filename 	= $item->filename;
				$attachment->title 		= $item->title;
				$attachment->thumb 		= $item->thumb;
				$attachment->url 		= $item->url;
				$attachment->type 		= $item->type;
				$attachment->object_id 	= $object->id;
				$attachment->signature 	= $item->signature;
				$attachment->save();

				$exist_photo[] 		= $item->signature;
				$this->save_union_data($object->id, $this->object_source_id, 'Object_Attachment', $attachment->id );
			}
		}
		return $this;
	}

	function copy_attributes()
	{
		$params = &$this->params;
		$object = &$this->object;
		$objects_for_union = &$this->objects_for_union;

		$exist_values = Array();
		$data = ORM::factory('Data_List')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = $item->value;

		$data = ORM::factory('Data_List')
					->join('object', 'left')
						->on('object.id', '=', 'object')
					->where("object.active","=",1)
					->where("object.is_published","=",1)
					->where('object', 'IN', array_values($objects_for_union))->find_all();
		foreach ($data as $item)
		{
			if ( !in_array($item->value, $exist_values) )
			{
				$data = ORM::factory('Data_List');
				$data->object 		= $object->id; 
				$data->reference 	= $item->reference; 
				$data->value 		= $item->value; 
				$data->attribute	= $item->attribute; 
				$data->save();

				$exist_values[] = $item->value;

				$this->save_union_data($object->id, $this->object_source_id, 'Data_List', $data->id );
			}
		}


		$exist_values = Array();
		$data = ORM::factory('Data_Integer')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = Array($item->reference ,$item->value_min, $item->value_max);		

		$data = ORM::factory('Data_Integer')
						->join('object', 'left')
							->on('object.id', '=', 'object')
						->where("object.active","=",1)
						->where("object.is_published","=",1)
						->where('object', 'IN', array_values($objects_for_union))->find_all();
		foreach ($data as $item)
		{
			if ( !in_array(Array($item->reference, $item->value_min, $item->value_max), $exist_values) )
			{
				$data = ORM::factory('Data_Integer');
				$data->object 		= $object->id; 
				$data->reference 	= $item->reference; 
				$data->value_min 	= $item->value_min; 
				$data->value_max 	= $item->value_max;
				$data->attribute	= $item->attribute; 
				$data->save();

				$exist_values[] = Array($item->reference ,$item->value_min, $item->value_max);

				$this->save_union_data($object->id, $this->object_source_id, 'Data_Integer', $data->id );
			}
		}


		$exist_values = Array();
		$data = ORM::factory('Data_Numeric')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = Array($item->reference, $item->value_min);		

		$data = ORM::factory('Data_Numeric')
						->join('object', 'left')
							->on('object.id', '=', 'object')
						->where("object.active","=",1)
						->where("object.is_published","=",1)
						->where('object', 'IN', array_values($objects_for_union))->find_all();
		foreach ($data as $item)
		{
			if ( !in_array(Array($item->reference, $item->value_min), $exist_values) )
			{
				$data = ORM::factory('Data_Numeric');
				$data->object 		= $object->id; 
				$data->reference 	= $item->reference; 
				$data->value_min 	= $item->value_min; 
				$data->value_max 	= $item->value_max;
				$data->attribute	= $item->attribute; 
				$data->save();

				$exist_values[] = Array($item->reference, $item->value_min);

				$this->save_union_data($object->id, $this->object_source_id, 'Data_Numeric', $data->id );
			}
		}
		

		/*$data = ORM::factory('Data_Text')->where('object', '=', $this->object_source_id)->find_all();
		foreach ($data as $item)
		{
			$data = ORM::factory('Data_Text');
			$data->object 		= $object->id; 
			$data->reference 	= $item->reference; 
			$data->value 		= $item->value; 
			$data->attribute	= $item->attribute; 
			$data->save();
		}
		$this->save_union_data($object->id, $this->object_source_id, 'Data_Text', $data->id );*/

		return $this;
	}

	function update_union_objects()
	{
		$object 		= &$this->object;
		$objects_for_union = &$this->objects_for_union;

		foreach (array_values($objects_for_union) as $item)
		{
			if ($item)
			{
				$obj = ORM::factory('Object', $item);	
				$obj->parent_id = $object->id;
				$obj->update();
			}
		}

		return $this;
	}

	function save_aditional_info()
	{
		$object 		= &$this->object;
		$object_source  = &$this->object_source;

		@list($min_price, $max_price) = ORM::factory('Data_Integer')
											->get_min_max_price($object->id);

		$count = ORM::factory('Object')
					->where("parent_id","=",$object->id)
					->where("active","=",1)
					->where("is_published","=",1)
					->count_all();

		$price_info	= '';
		if ($min_price == $max_price AND $min_price <> 0)
				$price_info = $count." ".Num::rus_suffix("предложение", $count)." по цене от ".$min_price." р.";
		elseif 
			($min_price <> $max_price AND $min_price <> 0)
				$price_info = $count." ".Num::rus_suffix("предложение", $count)." по цене от ".$min_price." до ".$max_price." р.";


		$main_image_id = NULL;
		$attachments = ORM::factory('Object_Attachment')
							->where('object_id', '=', $object->id)
							->order_by( DB::expr('random()') )
							->find_all();
		foreach($attachments as $item)
		{			
			$main_image_id = $item->id;
			break;
		}

		
		$data = ORM::factory('Object', $object->id);
		$data->is_union  		= $count;
		$data->title 	 		= $object_source->title;
		$data->action 	 		= $object_source->action;
		$data->user_text 		= $price_info;
		$data->main_image_id 	= $main_image_id;
		$data->update();

		return $this;
	}

	function delete_union_data()
	{
		$object 		= &$this->object;
		$object_source  = &$this->object_source;

		$ounion = ORM::factory('Object_Union')
						->where("object_union_id","=",$object->id)
						->where("object_id","=",$object_source->id)
						->find_all();
							Log::instance()->add(Log::NOTICE, "union ".$object->id." obj ".$object_source->id);
		foreach ($ounion as $union_param) {		
			Log::instance()->add(Log::NOTICE, "union_table ".$union_param->table." data_id ".$union_param->data_id);	
			DB::delete(strtolower($union_param->table))->where("id","=",$union_param->data_id)->execute();	
		}	
		DB::delete("object_union")->where("object_union_id","=",$object->id)
						->where("object_id","=",$object_source->id)->execute();
		return $this;
	}


	static function save_union_data($object_id, $object_source_id, $tablename, $data_id)
	{
		$ounion = ORM::factory('Object_Union');
		$ounion->object_union_id = $object_id;
		$ounion->object_id 		 = $object_source_id;		
		$ounion->table 			 = $tablename;
		$ounion->data_id 		 = $data_id;
		$ounion->save();	
	}

}