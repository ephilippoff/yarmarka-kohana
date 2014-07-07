<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddUnion extends Lib_PlacementAds_AddEdit {

	function Lib_PlacementAds_AddUnion($id){
		$this->init_defaults();
		$this->object_source_id = $id;
	}

	function save_city_and_addrress()
	{
		$params = &$this->params;
		$city = &$this->city;
		$location = &$this->location;
		$object = &$this->object;

		$city = ORM::factory('City', $params->city_id);

		$fulladdress = $city->region->title.', '.$city->title.', '.$params->address;

		@list($lon, $lat) = Ymaps::instance()->get_coord_by_name($fulladdress);

		$location = Address::save_address($lat, $lon,
 				$city->region->title,
 				$city->title,
 				$params->address
 			);

		// если не нашли адрес, то берем location города
		if ( ! $location->loaded())
		{
			$location = $city->location;
		}

		return $this;
	}

	function copy_photo()
	{
		$params = &$this->params;
		$object = &$this->object;

		$exist_photo = Array();
		$data = ORM::factory('Object_Attachment')->where('object_id', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_photo[] = $item->signature;

		$oa = ORM::factory('Object_Attachment')->where('object_id', '=', $this->object_source_id)->find_all();
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

				$this->save_union_data($object->id, $this->object_source_id, 'Object_Attachment', $attachment->id );
			}
		}
		return $this;
	}

	function copy_attributes()
	{
		$params = &$this->params;
		$object = &$this->object;

		$exist_values = Array();
		$data = ORM::factory('Data_List')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = $item->value;

		$data = ORM::factory('Data_List')->where('object', '=', $this->object_source_id)->find_all();
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

				$this->save_union_data($object->id, $this->object_source_id, 'Data_List', $data->id );
			}
		}

		$exist_values = Array();
		$data = ORM::factory('Data_Integer')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = Array($item->reference ,$item->value_min, $item->value_max);
		

		$data = ORM::factory('Data_Integer')->where('object', '=', $this->object_source_id)->find_all();
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

				$this->save_union_data($object->id, $this->object_source_id, 'Data_Integer', $data->id );
			}
		}

		$exist_values = Array();
		$data = ORM::factory('Data_Numeric')->where('object', '=', $object->id)->find_all();
		foreach ($data as $item)
			$exist_values[] = Array($item->reference, $item->value_min);
		
		

		$data = ORM::factory('Data_Numeric')->where('object', '=', $this->object_source_id)->find_all();
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

	function save_aditional_info()
	{
		$object = &$this->object;

		$source_object = ORM::factory('Object', $this->object_source_id);

		@list($min_price, $max_price) = ORM::factory('Data_Integer')
											->get_min_max_price($this->object_source_id);

		$count = ORM::factory('Object')
					->where("parent_id","=",$object->id)
					->where("active","=",1)
					->where("is_published","=",1)
					->count_all();

		$price_info	= '';
		if ($min_price == $max_price AND $min_price <> 0)
				$price_info = $count." предложений по цене от ".$min_price." р.";
		elseif 
			($min_price <> $max_price AND $min_price <> 0)
				$price_info = $count." предложений по цене от ".$min_price." до ".$max_price." р.";

		$data = ORM::factory('Object', $object->id);
		$data->is_union  = $count;
		$data->title 	 = $source_object->title;
		$data->user_text = $price_info;
		$data->update();
	}


	static function save_union_data($object_id, $source_object_id, $tablename, $data_id)
	{
		$ounion = ORM::factory('Object_Union');
		$ounion->object_union_id = $object_id;
		$ounion->object_id 		 = $source_object_id;		
		$ounion->table 			 = $tablename;
		$ounion->data_id 		 = $data_id;
		$ounion->save();	
	}

}