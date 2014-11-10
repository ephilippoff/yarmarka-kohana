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
			else
				$data[] = $this->get_default_field($field, $values->{$field->name});
		}

		return $data;

	}

	public function validation_prepare(array $data)
	{
		$this->validation = Validation::factory($data);
		foreach ($data as $key => $value) {
			$field = new Obj($this->_settings["fields"][$key]);
			$title = $field->translate;
			$required = $field->required;
			if ($required)
				$this->validation->rule( $key, 'not_empty', array(':value', $title) );
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
						"type"  => $field->type
					);
	}

	private function get_photo_field(Obj $field, $value = NULL)
	{
		return array(	"title" => $field->translate,
						"name"	=> $field->name,
						"html"  => Form::file($field->name,  array("class" => "form-control")).Form::hidden($field->name, (($value)?$value:1)),
						"required" => $field->required,
						"path" => $value,
						"type"  => $field->type
						);
	}

}