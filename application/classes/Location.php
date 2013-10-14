<?php defined('SYSPATH') OR die('No direct script access.');

class Location
{
	public static function addLocation($lon, $lat, $address, $city_kladr_id, $address_kladr_id)
	{
		$kladr_city = Model::factory('Kladr')->get_city_by_id($city_kladr_id);
		$kladr_address = Model::factory('Kladr')->get_address_by_id($address_kladr_id);

		$location = ORM::factory('Location', array('lon' => $lon, 'lat' => $lat));

		if ( ! $location->loaded())
		{
			$location->lon = $lon;
			$location->lat = $lat;
			$location->address = $address;
			if ($kladr_city)
			{
				$location->region = $kladr_city->region;
				$location->city = $kladr_city->city;
			}
			if ($kladr_address)
			{
				$housenum = $kladr_address->housenum.($kladr_address->buildnum ? ', '.$kladr_address->buildnum : '');

				/*$location->housenum = $housenum;
				$location->street = $kladr_address->address;*/
			}
			$location->save();
		}

		return $location;
	}

	public static function addLocationByPostParams()
	{
		$lon = Request::current()->post('lon');
		$lat = Request::current()->post('lat');
		$city_kladr_id = Request::current()->post('city_kladr_id');
		$address_kladr_id = Request::current()->post('address_kladr_id');
		$address = Request::current()->post('address');

		return self::addLocation($lon, $lat, $address, $city_kladr_id, $address_kladr_id);
	}
}

/* End of file Location.php */
/* Location: ./application/classes/Location.php */