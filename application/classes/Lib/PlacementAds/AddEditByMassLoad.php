<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEditByMassLoad extends Lib_PlacementAds_AddEdit {

	function init_object_and_mode()
	{
		$params = &$this->params;
		$object = &$this->object;
		$user = &$this->user;
		if ($params->end_user_id)
		{
			$user = ORM::factory('User', $params->end_user_id);
		}
		if ($params->external_id)
		{
			$object = ORM::factory('Object')
						->where("author","=",$user->id)
						->where("number","=",$params->external_id)
						->where("category","=",$params->rubricid)
						->where("active","=",1)
						->find();
			if ($object->loaded())
			{
				$this->is_edit = TRUE;
			}
		}
		return $this;
	}

	function save_generated()
	{
		$object = &$this->object;
		$params = &$this->params;

		$object->title = $object->generate_title();

		$object->full_text = $object->generate_full_text();
		$object->save();

		ORM::factory('Data_Integer')->save_price_per_square($object->id, $object->category);

		return $this;
	}

	function save_geoloc()
	{
		$params = &$this->params;
		$city = &$this->city;
		$object = &$this->object;

		$city = ORM::factory('City', $params->city_id);

		$fulladdress = $city->region->title.', '.$city->title.', '.$params->address;

		@list($coords, $yregion, $ycity) = Ymaps::instance()->get_coord_by_name($fulladdress);
		@list($lon, $lat) = $coords; 

		$object->geo_loc = sprintf('%s,%s', $lat, $lon);

		return $this;
	}

	function save_massload_info($massload_id)
	{
		$params = &$this->params;
		$object = &$this->object;

		$om = ORM::factory('Object_Massload')
					->where("massload_id", "=", $massload_id)
					->where("external_id", "=", $params->external_id)
					->find();
		$om->object_id 		= $object->id;
		$om->massload_id 	= $massload_id;
		$om->is_edit 		= ($this->is_edit) ? 1 : 0;
		$om->save();
	}

	function init_contacts()
	{
		$contacts = &$this->contacts;
		foreach((array) $this->params as $key=>$value){
			if (preg_match('/^contact_([0-9]*)_value/', $key, $matches))
			{
				$contact_type 	= ORM::factory('Contact_Type');
				$value = trim($this->params->{'contact_'.$matches[1].'_value'});
				if (Valid::email($value))
					$type = 5;
				else
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

	function save_contacts()
	{
		$object = &$this->object;
		$user = &$this->user;
		$contacts = &$this->contacts;

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
		}

		return $this;
	}

	function save_external_info()
	{
		$object = &$this->object;
		$params = &$this->params;
		$object->number = $params->external_id;
		$object->is_published = 1;

		return $this;
	}

	function save_photo()
	{
		return $this;
		$params = &$this->params;
		$object = &$this->object;

		$urls = $params->userfile_urls;

		// удаляем старые аттачи
		// @todo по сути не надо заного прикреплять те же фотки при редактировании объявления
		ORM::factory('Object_Attachment')->where('object_id', '=', $object->id)->fdelete();

		// собираем аттачи
		if ($userphotos = $params->userfile AND is_array($userphotos))
		{
			$main_photo = $params->active_userfile;
			if ( ! $main_photo AND isset($userphotos[0]))
			{
				$main_photo = $userphotos[0];
			}

			foreach ($userphotos as $file)
			{
				$url = array_shift($urls);
				$attachment = ORM::factory('Object_Attachment');
				$attachment->filename 	= $file;
				$attachment->object_id 	= $object->id;
				$attachment->signature 	= TRUE;
				$attachment->url = $url["url"];
				$attachment->title = $url["title"];
				$attachment->save();

				if ($file == $main_photo)
				{
					$object->main_image_id = $attachment->id;
				}
			}

			// удаляем аттачи из временой таблицы
			foreach ($userphotos as $file) 
			{
				ORM::factory('Tmp_Img')->delete_by_name($file);
			}
		}
		return $this;
	}

	function save_style_object()
	{
		$params = &$this->params;
		$object = &$this->object;
		

		if ($params->set_no_company) {
			$object->style = 'p';
		} else {
			$object->style = NULL;
		}

		$object->moder_state = 4;
		
		return $this;
	}

}