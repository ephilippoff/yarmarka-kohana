<?php defined('SYSPATH') OR die('No direct script access.');

class Address
{

	public static function save_address($lat, $lon, $region_str, $city_str, $address_str)
	{
		$location = ORM::factory('Location');

		if ( ! $lat OR ! $lon)
		{
			// если координаты не пришли, запрашиваем координаты по адресу
			@list($coords, $yregion, $ycity) = Ymaps::instance()->get_coord_by_name($region_str.', '.$city_str.', '.$address_str);
			if ($coords)
				list($lon, $lat) = $coords;
		}

		// ищем location адреса по координатам
		if ($lat AND $lon AND $address_str)
		{
				$location = ORM::factory('Location');
				$location->region 	= $region_str;
				$location->city 	= $city_str;
				$location->address 	= $address_str;
				$location->lat 		= $lat;
				$location->lon 		= $lon;
				$location->save();
		}

		return $location;
	}

	
}

/* End of file Address.php */
/* Location: ./application/classes/Address.php */