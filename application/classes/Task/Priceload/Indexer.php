<?php defined('SYSPATH') or die('No direct script access.');


class Task_Priceload_Indexer extends Minion_Task
{
	protected $_options = array(
		"one" => 1
	);

	protected function _execute(array $params)
	{
		$one 				= $params['one'];

		Minion_CLI::write('indexer start');

		ORM::factory('Priceload_Index')->delete_all();

		$pl = ORM::factory('Priceload')
				->where("state", "=", 2)
				->where("table_name", "IS NOT", NULL)
				->find_all();

		foreach($pl as $priceload)
		{
			$pi = ORM::factory('Priceload_Index')
							->where("priceload_id","=",$priceload->id)
							->find();
			if ($pi->loaded())
				continue;

			Minion_CLI::write($priceload->table_name);
			$category_id = 0;
			$city_id = 0;

			$object = ORM::factory('Object_Priceload')
								->select("object.active","object.is_published","object.category","object.city_id")
								->join("object")
									->on("object_priceload.object_id","=","object.id")
								->where("priceload_id","=",$priceload->id)
								->where("object.active","=",1)
								->where("object.is_published","=",1)
								->cached(60)
								->find();
			if (!$object->loaded() OR $object->active = 0 OR $object->is_published = 0)
				continue;

			if ($object->loaded())
			{
				$category_id = $object->category;
				$city_id 	 = $object->city_id;
			}

			$object_id = $object->object_id;
		
			//$pricerows = ORM_Temp::factory($priceload->table_name)->find_all();
			$pricerows =  DB::select()->from("_temp_".$priceload->table_name)
								->order_by("id","asc")
								->as_object()
								->execute();
			if (!$priceload->config)
				continue;

			$fields = array_keys(ORM_Temp::factory($priceload->table_name)->list_columns());
			$service_fields = Priceload::getServiceFields();
			$fields = array_diff($fields, array_keys($service_fields), array("id"));

			$config = unserialize($priceload->config);

			foreach ($pricerows as $pricerow) {
				$description = NULL;
				$price = NULL;
				$idx_text = array();
				$fullrow = array();
				foreach ($fields as $field) {
					if ($config[$field."_type"] <> "no")
						$idx_text[] = $pricerow->{$field};
					if ($config[$field."_type"] == "description")
						$description =  $pricerow->{$field};
					if ($config[$field."_type"] == "price")
						$price =  $pricerow->{$field};

					$fullrow[$field] = $pricerow->{$field};
				}
				if (count($idx_text) == 0 OR !$description)
					continue;

				
				$idx_text = implode(",", $idx_text);

				if ($priceload->keywords)
					$idx_text = $priceload->keywords." ".$idx_text;
				
				$idx_text = Text::remove_symbols($idx_text);

				$plidx = ORM::factory('Priceload_Index');
				$plidx->text = $idx_text;
				$plidx->priceload_id =  $priceload->id;
				$plidx->pricerow_id = $pricerow->id;
				$plidx->category_id = $category_id;
				$plidx->city_id = $city_id;
				$plidx->object_id = $object_id;
				$plidx->description = $description;
				$plidx->price = (int) $price;
				$plidx->image = $pricerow->image;
				$plidx->full = serialize($fullrow);
				$plidx->save();	

			}

			$this->createFilters($priceload->id);
		}

		

		//Temptable::delete_table($name);
	}

	function createFilters($priceload_id)
	{
		Minion_CLI::write('create filters '.$priceload_id);

		$dquery = DB::select("priceload_idata.id")->from("priceload_idata")
						->join("priceload_attribute","left")
							->on("priceload_attribute_id","=","priceload_attribute.id")
						->where("priceload_attribute.priceload_id","=",$priceload_id);

		ORM::factory('Priceload_Idata')
			->where("id","IN",$dquery)
			->delete_all();

		$pfilter = ORM::factory('Priceload_Filter')
						->where("priceload_id","=",$priceload_id)
						->where("count",">",0)
						->order_by("priceload_id","desc")
						->order_by("priceload_attribute_id","asc")
						->find_all();
			
		foreach ($pfilter as $filter) {
			
			$filtered_rows = unserialize($filter->filtered_rows);
			if (!count($filtered_rows))
			{
				Minion_CLI::write('!!!!!! filter:'.$filter->title.' ('.$filter->count.')');
				continue;
			}

			$pidata = ORM::factory('Priceload_Idata');
			$query = DB::select("id", array(DB::expr($filter->id),"filter"), array(DB::expr($filter->priceload_attribute_id),"attribute"))
						->from('priceload_index')
						->where("pricerow_id","IN", $filtered_rows)
						->where("priceload_id","=",$filter->priceload_id);

			DB::insert('priceload_idata', array('priceload_index_id', 'priceload_filter_id', 'priceload_attribute_id'))
					->select( $query )
					->execute();
	
			Minion_CLI::write('process filter:'.$filter->title.' ('.$filter->count.')');
		}
		

		Minion_CLI::write('indexer stop');
	}

}
