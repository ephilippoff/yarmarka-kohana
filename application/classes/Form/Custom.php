<?php defined('SYSPATH') or die('No direct script access.');

class Form_Custom  {

	public $_settings = NULL;
	public $_instance = NULL;
	public $errors    = NULL;
	private $validation = NULL;

	public static function factory($name)
	{
		
		// Add the model prefix
		$class = 'Form_Custom_'.$name;

		return new $class;
	}

	public function prerender(array $values = NULL)
	{
		if (!$values)
			$values = array();

		$data = array();
		$fields = $this->_settings["fields"];
		$values = new Obj($values);
		foreach ($fields as $field) {
			$field = new Obj($field);

			if ($field->type == "photo")
				$data[] = $this->get_photo_field($field, $values->{$field->name});
			elseif ($field->type == "text")
				$data[] = $this->get_text_field($field, $values->{$field->name});
			else
				$data[] = $this->get_default_field($field, $values->{$field->name});
		}

		return $data;

	}

	public function validation_prepare(array $data)
	{
		$this->validation = Validation::factory($data);
		foreach ($data as $key => $value) {
			if (!array_key_exists($key, $this->_settings["fields"]))
				continue;
			$field = new Obj($this->_settings["fields"][$key]);
			$title = $field->translate;
			$required = $field->required;
			if ($required)
				$this->validation->rule( $key, 'not_empty', array(':value', $title) );
			if ($field->type == "inn")
				$this->validation->rule( $key, 'inn', array(':value', $title) );
		}
	}

	public function save(array $data)
	{
		$this->validation_prepare($data);
		if ( !$this->validation->check() ){
			$this->errors = $this->validation->errors('validation/object_form');
			return FALSE;
		}

		return TRUE;
	}

	private function get_default_field(Obj $field, $value = NULL)
	{
		return array(	"title" => $field->translate,
						"name"	=> $field->name,
						"html"  => Form::input($field->name, $value,  array("class" => "form-control")),
						"required" => $field->required,
						"value" => $field->value,
						"type"  => $field->type,
						"description"  => $field->description
					);
	}

	private function get_text_field(Obj $field, $value = NULL)
	{
		return array(	"title" => $field->translate,
						"name"	=> $field->name,
						"html"  => Form::textarea($field->name, $value,  array("class" => "form-control")),
						"required" => $field->required,
						"value" => $field->value,
						"type"  => $field->type,
						"description"  => $field->description
					);
	}

	private function get_photo_field(Obj $field, $value = NULL)
	{
		$file_path = "";
		if (!$value AND array_key_exists($field->name, $_FILES) AND $_FILES[$field->name]["size"])
		{
			$filename = Uploads::make_thumbnail($_FILES[$field->name]);
			$paths = Imageci::getSitePaths($filename);
			$file_path = $paths[$field->size];
		} else {
			$file_path = $value;
		}
		return array(	"title" => $field->translate,
						"name"	=> $field->name,
						"html"  => Form::file($field->name,  array("id" => $field->name, "class" => "form-control")).Form::hidden($field->name, $file_path, array("class" => $field->name."_hidden")),
						"required" => $field->required,
						"path" => $file_path,
						"type"  => $field->type,
						"description"  => $field->description
					);
	}

}