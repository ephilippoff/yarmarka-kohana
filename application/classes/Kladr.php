<?php defined('SYSPATH') OR die('No direct script access.');

class Kladr
{
	public static function save_city($city_kladr_id, $city_name)
	{
		// ищем и сохраняем город и улицу по kladr_id
		$object_city = ORM::factory('City')
			->where('kladr_id', '=', $city_kladr_id)
			->find();

		$city_kladr_row = Model::factory('Kladr')->get_city_by_id($city_kladr_id);
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
				$object_city->kladr_id 		= $city_kladr_id;
				$object_city->region_id 	= $region->id;
				$object_city->geo_loc 		= $coord;
				$object_city->location_id 	= $location_id;
				$object_city->save();
			}
			elseif ($coord) // если нашли координаты
			{
				// обновляем location_id у старого
				$object_city->location = $location;
				$object_city->save();
			}
		}

		return $object_city;
	}

	public static function save_address($address_kladr_id, $object_coordinates, $address_str)
	{
		$location = ORM::factory('Location');

		// ищем location адреса по координатам
		if ($object_coordinates AND $address_str)
		{
			list($lat, $lon) = explode(',', $object_coordinates);
			if ($lat AND $lon)
			{
				$location = $this->Location_m->get_by_coord_with_kladr_id($lat, $lon);
				$location = ORM::factory('Location')->where_lat_lon($lat, $lon)
					->where('kladr_id', 'IS', DB::expr('NOT NULL'))
					->find();
				if ( ! $location->loaded() OR ! $location->address)
				{
					$level = $kladr_id = NULL;
					if ($address_kladr_id)
					{
						// берем адрес из КЛАДР
						$address_kladr_row = ORM::factory('Kladr')->get_address_by_id($address_kladr_id);
						$address_str = Kladr::collect_address($address_kladr_row);
						$level = $address_kladr_row->aolevel;
						$kladr_id = $address_kladr_row->id;
					}

					$location = ORM::factory('Location');
					$location->region 	= $city_kladr_row->region;
					$location->city 	= $city_kladr_row->city;
					$location->address 	= $address;
					$location->level 	= $level;
					$location->kladr_id = $kladr_id;
					$location->lat 		= $lat;
					$location->lon 		= $lon;
					$location->save();
				}
			}
		}

		return $location;
	}

	public static function collect_address($row)
	{
		$address_str = $row->address;
		if ($row->housenum)
		{
			$address_str .= ', д. '.$row->housenum;
		}
		if ($row->buildnum)
		{
			$address_str .= ', корп. '.$row->buildnum;
		}
		if ($row->strucnum)
		{
			$address_str .= ', стр. '.$row->strucnum;
		}

		return $address_str;
	}
}

/* End of file Kladr.php */
/* Location: ./application/classes/Kladr.php */