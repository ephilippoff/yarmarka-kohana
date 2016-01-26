<?php defined('SYSPATH') or die('No direct script access.');

class Lib_PlacementAds_AddEditByModerator extends Lib_PlacementAds_AddEdit {

	//исключаем проверку контактов модератору
	function init_validation_rules()
	{
		$category = &$this->category;
		$validation = &$this->validation;
		$params = &$this->params;
		$category_settings  = new Obj((array) $this->category_settings);

		$validation = Validation::factory((array) $this->params)
			->rule('city_id', 'not_empty', array(':value', "Город"))
			->rule('rubricid', 'not_empty', array(':value', "Раздел"))
			->rule('rubricid', 'not_category_0', array(':value', "Раздел"));

		// if ($category)
		// {
		// 	$validation->rule('contact', 'not_empty', array(':value', "Контактное лицо"));
		// }

		if ($category AND !$category->title_auto_fill AND !$params->itis_massload)
		{
			$validation->rules('title_adv', array(
				array('not_empty', array(':value', "Заголовок")),
				array('min_length', array(':value', 10, "Заголовок")),
			));
		}

		if ($category AND $category->text_required)
		{
			$validation->rules('user_text_adv', array(
				array('not_empty_html', array(':value', "Текст объявления")),
				array('max_length', array(':value', 15000, "Текст объявления")),
			));
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
			// сохраняем контакты для пользователя
			$user->add_verified_contact($contact['type_id'], $contact['value'], TRUE, TRUE);

			// сохраянем новые контакты для объявления
			$object->add_contact($contact['type_id'], $contact['value']);
		}

		return $this;
	}



	function exec_validation()
	{
		$errors = &$this->errors;
		$user = &$this->user;

		//заполнены ли обязательные параметры
		if ( !$this->validation->check())
		{
			if (!$errors)
				$errors = array();

			$errors = array_merge($errors, $this->validation->errors('validation/object_form'));
		}

		// указаны ли контакты
		// if ( ! count($this->contacts))
		// {
		// 	$errors['contact_mobile'] = "Необходимо добавить хотя бы один верифицированный контакт для связи (Мобильный телефон)";
		// }

		return $this;
	}

	function save_typetr_object()
	{
		$params = &$this->params;
		$object = &$this->object;
		
		$object->type_tr = $params ->obj_type;

		return $this;
	}

	function save_dates_object()
	{
		$params = &$this->params;
		$object = &$this->object;
		
		if (!$params->_date_created) {
			return $this;
		}
		$created = date_create($params->_date_created);
		$time_created = date_create($params->_time_created);
		date_time_set($created, (int)  date_format($time_created, 'H'), (int)  date_format($time_created, 'i'));

		$expired = date_create($params->_date_expired); 
		$time_expired = date_create($params->_time_expired);
		date_time_set($expired, (int) date_format($time_expired, 'H'), (int) date_format($time_expired, 'i'));

		// $expiration = date_create($params->_date_expiration); 
		// $time_expiration = date_create($params->_time_expiration);
		// date_time_set($expiration, (int) date_format($time_expiration, 'H'), (int) date_format($time_expiration, 'i'));

		$object->date_created = date_format($created, "Y-m-d H:i");
		$object->date_expired = date_format($expired, "Y-m-d H:i");
		//$object->date_expiration = date_format($expiration, "Y-m-d H:i");

		return $this;
	}

	function init_validation_rules_for_attributes()
	{

		return $this;		
	}

	function init_additional()
	{
		$category = &$this->category;
		$validation = &$this->validation;
		$params = &$this->params;

		if ($category AND $settings = Kohana::$config->load("category.".$category->id.".additional_fields.2"))
		{

			$titles =  Kohana::$config->load("dictionaries.additional_fields.2");
			foreach ($settings as $setting) {
				$validation->rules($setting, array(
						array('not_empty', array(':value', $titles[$setting]))
					)
				);
			}
		}

		if ($category AND $saveas = Kohana::$config->load("category.".$category->id.".additional_saveas"))
		{
			if (!$saveas)
				$saveas = array();

			foreach ($saveas as $field => $_saveas) {

					$param = $saveas[$field][0];
					$value = trim($params->{$field});
					if ($value)
						$params->{$param} = $value;
					else
						$params->{$param} = $saveas[$field][1];
			}
		}
		return $this;
	}

	function save_additional()
	{
		$params = &$this->params;
		$object = &$this->object;

		if ($object->category AND $settings = Kohana::$config->load("category.".$object->category.".additional_fields.2"))
		{
			$additional = array();
			foreach ($settings as $setting) {
				$name = str_replace("additional_", "", $setting);
				$value = $params->{$setting};

				// if ($this->is_edit AND $this->user_id) {

				// 	if (!$value)
				// 		ORM::factory('User_Settings')
				// 			->_delete($this->user_id, "orginfo", $name);
					
				// 	ORM::factory('User_Settings')
				// 			->update_or_save($this->user_id, "orginfo", $name, $value);

				// }

				$additional[$setting] = $value;
			}
			$additional = ORM::factory('Data_Additional')->set_additional($object->id, $additional);
		}

		return $this;
	}

}