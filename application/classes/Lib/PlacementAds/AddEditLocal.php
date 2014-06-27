<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEditLocal extends Lib_PlacementAds_AddEdit {

	function save_city_and_addrress()
	{
		$city = &$this->city;
		$location = &$this->location;

		$city = new stdClass();
		$city->id = 1947;

		$location = ORM::factory('Location');

		return $this;
	}

}