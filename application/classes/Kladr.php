<?php defined('SYSPATH') OR die('No direct script access.');

class Kladr
{
	public static function save_address(Request $request)
	{
		// @todo доабвить транзакцию

		$city_kladr_id 		= trim($request->post('city_kladr_id'));
		$address_kladr_id 	= trim($request->post('address_kladr_id'));

		// ищем и сохраняем город и улицу по kladr_id
		$object_city = ORM::factory('City')
			->where('kladr_id', '=', $city_kladr_id)
			->find();
		$city_name = $request->post('city_name');


		$city_kladr_row = ORM::factory('Kladr')->get_city_by_id($city_kladr_id);
		$region = ORM::factory('Region')->where('kladr_id', '=', $city_kladr_row->region_id);
		if ( ! $region)
		{
			// добавляем новый регион
			$region = ORM::factory('Region');
			$region->title 		= $city_kladr_row->region;
			$region->kladr_id 	= $city_kladr_row->region_id;
			$region->kladr_code = $city_kladr_row->region_code;
			$region->is_visible = 0;
			$region->save();

			// @todo может быть нужен будет $region->reload()

			$region_id = $region->id;
		}
		else
		{
			$region_id = $region->id;
		}

		// города нет в нашей базе или у города нет location
		if ( ! $object_city->loaded() OR ! $object_city->location_id)
		{
			// ищем координаты города
			$coord = Ymaps::instance()->get_coord_by_name($city_kladr_row->city);
			if ($coord)
			{
				// добавляем координату города в locations
				list($lon, $lat) = $coord;
				$location_id = $this->Location_m->add($city_kladr_row->region, $city_kladr_row->city, NULL, NULL, $city_kladr_id, $lat, $lon);

				$location = ORM::factory('Location');
				$location->region 	= $city_kladr_row->region;
				$location->city 	= $city_kladr_row->city;
				$location->kladr_id = $city_kladr_id;
				$location->lat 		= $lat;
				$location->lon 		= $lon;
				$location->save();

				$coord = join(',', $coord);
			}

			if ( ! $object_city)
			{
				// сохраняем новый город с location_id
				$object_city->title 		= $object_city->sinonim = $city_name;
				$object_city->is_visible 	= 0;
				$object_city->kladr_id 	= $city_kladr_id;
				$object_city->region_id 	= $region_id;
				$object_city->geo_loc 		= $coord;
				$object_city->location_id 	= $location_id;
				$object_city->save();
			}
			else 
			{
				// обновляем location_id у старого
				$object_city->location_id = $location_id;
				$object_city->save();
			}
		}

		// @todo дальше еще надо доделать
		$address_str = trim($this->input->post('address'));
		// ищем location адреса по координатам
		if ($geo_location AND $address_str)
		{
			list($lat, $lon) = explode(',', $geo_location);
			if ($lat AND $lon)
			{
				$location = $this->Location_m->get_by_coord_with_kladr_id($lat, $lon);
				if ($location AND $location->address)
				{
					$location_id = $location->id;
				}
				else
				{
					$level = $kladr_id = NULL;
					if ($address_kladr_id)
					{
						// берем адрес из КЛАДР
						$address_kladr_row = $this->kladr_m->get_by_id($this->input->post('address_kladr_id'));
						$address_str = Kladr_helper::collect_address($address_kladr_row);
						$level = $address_kladr_row->aolevel;
						$kladr_id = $address_kladr_row->id;
					}

					$location_id = $this->Location_m->add(
						$city_kladr_row->region, 
						$city_kladr_row->city, 
						$address_str, 
						$level,
						$kladr_id,
						$lat, 
						$lon);
				}
			}
		}
		else
		{
			$geo_location 	= $object_city->geo_loc;
			$location_id 	= $object_city->location_id;
		}
	}
}

/* End of file Kladr.php */
/* Location: ./application/classes/Kladr.php */