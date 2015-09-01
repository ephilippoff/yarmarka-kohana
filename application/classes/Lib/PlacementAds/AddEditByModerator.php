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

		if ($category)
		{
			$validation->rule('contact', 'not_empty', array(':value', "Контактное лицо"));
		}

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
		if ( ! count($this->contacts))
		{
			$errors['contact_mobile'] = "Необходимо добавить хотя бы один верифицированный контакт для связи (Мобильный телефон)";
		}

		return $this;
	}

	function save_typetr_object()
	{
		$params = &$this->params;
		$object = &$this->object;
		
		$object->type_tr = $params ->obj_type;

		return $this;
	}

}