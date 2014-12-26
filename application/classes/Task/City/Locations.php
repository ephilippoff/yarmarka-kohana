<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Находит и сохраняет locations для городов в таблице city
 */
class Task_City_Locations extends Minion_Task
{
	protected $_options = array(
	);

	protected function _execute(array $params)
	{
		$cities = ORM::factory('City')->find_all();
		foreach ($cities as $city)
		{
			if ($city->location_id)
			{
				Minion_CLI::write('City '.Minion_CLI::color($city->title, 'cyan').', location exists');
			}
			else
			{
				Minion_CLI::write('Looking for location for city '.Minion_CLI::color($city->title, 'cyan'));
				$location = $this->get_location_for_city($city);
				if ( ! $location)
				{
					Minion_CLI::write(Minion_CLI::color('Error: can\'t get location for city '.$city->title, 'red'));
				}
				else
				{
					$city->location_id = $location->id;
					$city->save();

					Minion_CLI::write('Location id'.Minion_CLI::color($location->id, 'cyan').' added to city '.Minion_CLI::color($city->title, 'cyan'));
				}
			}
		}
	}

	public function get_location_for_city(Model_City $city)
	{
		@list($coords, $yregion, $ycity) = Ymaps::instance()->get_coord_by_name($city->region->title.', '.$city->title);
		if ( ! $coords)
		{
			Minion_CLI::write(Minion_CLI::color('Coords not found', 'red'));
			return FALSE;
		}
		else
		{
			Minion_CLI::write('Coords for city '.Minion_CLI::color($city->title, 'cyan').' is '.Minion_CLI::color(print_r($coords, TRUE), 'cyan'));
		}

		list($lon, $lat) = $coords;
		$location = ORM::factory('Location')->where_lat_lon($lat, $lon)->find();
		if ( ! $location->loaded())
		{
			$location = ORM::factory('Location');
			$location->region 	= $city->region->title;
			$location->city 	= $city->title;
			$location->kladr_id = $city->kladr_id;
			$location->lat 		= $lat;
			$location->lon 		= $lon;
			$location->save();

			Minion_CLI::write('Create new location, new id '.Minion_CLI::color($location->id, 'cyan'));
		}

		return $location;
	}
}