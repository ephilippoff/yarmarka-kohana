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

		

		$pl = ORM::factory('Priceload')
				->select("object_id")
				->join("object_priceload")
					->on("priceload.id","=","priceload_id")
				->where("state", "=", 2)
				->find_all();
		Minion_CLI::write(count($pl));
		foreach($pl as $priceload)
		{
			Minion_CLI::write($priceload->table_name);
			$category_id = 0;
			$city_id = 0;
			$object = ORM::factory('Object', $priceload->object_id)->cached(60);
			if ($object->loaded())
			{
				$category_id = $object->category;
				$city_id 	 = $object->city_id;
			}
		
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
				$idx_text = Array();
				foreach ($fields as $field) {
					if ($config[$field."_type"] <> "no")
						$idx_text[] = $pricerow->{$field};
					if ($config[$field."_type"] == "description")
						$description =  $pricerow->{$field};
					if ($config[$field."_type"] == "price")
						$price =  $pricerow->{$field};
				}
				if (count($idx_text) == 0 OR !$description)
					continue;

				$idx_text = implode(",",$idx_text);
				
				$plidx = ORM::factory('Priceload_Index');
				$plidx->text = $idx_text;
				$plidx->priceload_id =  $priceload->id;
				$plidx->pricerow_id = $pricerow->id;
				$plidx->category_id = $category_id;
				$plidx->city_id = $city_id;
				$plidx->object_id = $priceload->object_id;
				$plidx->description = $description;
				$plidx->price = $price;
				$plidx->save();	
			}
		}
		

		Minion_CLI::write('indexer stop');

		//Temptable::delete_table($name);
	}

}
