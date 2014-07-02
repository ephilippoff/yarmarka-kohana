<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEditByMassLoad extends Lib_PlacementAds_AddEdit {

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

	function init_contacts()
	{
		$contacts = &$this->contacts;
		foreach((array) $this->params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$contact_type 	= ORM::factory('Contact_Type');
				$value = trim($this->params->{'contact_'.$matches[1].'_value'});
				$value = "7".$value;
				$type = $contact_type->detect_contact_type( Text::clear_phone_number($value) );

				if ($value)
				{
					$contact_type 	= ORM::factory('Contact_Type', $type );

					if ($contact_type->loaded())
					{
						$contacts[] = array(
							'value' 		=> $value,
							'type' 			=> $contact_type->id,
							'type_name' 	=> $contact_type->name,
						);
					}
				}
			}
		}
		return $this;
				
	}



}