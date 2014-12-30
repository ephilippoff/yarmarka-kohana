<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEditLocal extends Lib_PlacementAds_AddEdit {

	function save_address()
	{
		$city = &$this->city;
		$location = &$this->location;

		$city = new stdClass();
		$city->id = 1947;

		$location = ORM::factory('Location');

		return $this;
	}

	function init_contacts()
	{
		$contacts = &$this->contacts;
		foreach((array) $this->params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$value = trim($this->params->{'contact_'.$matches[1].'_value'});
				$type = $this->params->{'contact_'.$matches[1].'_type'};
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

	function save_contacts()
	{
		$object = &$this->object;
		$user = &$this->user;
		$contacts = &$this->contacts;

		$object_compile = &$this->object_compile;
		$object_compile["contacts"] 	= array();

		if ($this->is_edit)
		{	
			// удаляем связи на старые контакты
			$object->delete_contacts();
		}

		foreach ($contacts as $contact)
		{
			// сохраняем контакты для модератора но не привязываем к учетке
			$user->add_contact($contact['type'], $contact['value'], 0, 1);
			// сохраянем новые контакты для объявления
			$object->add_contact($contact['type'], $contact['value']);

			$object_compile["contacts"][] = array("type" => $contact['type'], "value" => $contact['value']);
		}

		return $this;
	}

	function exec_validation()
	{
		$errors = &$this->errors;
		$user = &$this->user;
		//заполнены ли обязательные параметры
		if ( ! $this->validation->check())
		{
			$errors = $this->validation->errors('validation/object_form');
		}

		// указаны ли контакты
		if ( ! count($this->contacts))
		{
			$errors['contacts'] = Kohana::message('validation/object_form', 'empty_contacts');
		}

		return $this;
	}

}