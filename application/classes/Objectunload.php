<?php defined('SYSPATH') or die('No direct script access.');

class Objectunload 
{
	public $_category = NULL;
	public $_user_id = NULL;
	public $_limit = 0;

	public function __construct($user_id, $category)
	{
		$this->_category = $category;
		$this->_user_id  = $user_id;
		$this->_limit	 = Kohana::$config->load('massload.free_limit');
	}

	public function get_objects($limit = NULL)
	{
		$user_id  = $this->_user_id;
		$category = $this->_category;

		if (!$limit)
			$limit 	  = $this->_limit;

		$config = Kohana::$config->load('massload/bycategory.'.$category);

		$query = ORM::factory('Object')
						->where("author", "=", $user_id)
						->where("category", "=", $config["id"]);

		$filters = Search::get_filters_by_params($config["filter"]);
		foreach ($filters as $filter)
			$query = $query->where("0", "<", $filter);

		$result = $query->where("is_published", "=", 1)
						->limit($limit)
					    ->order_by("date_created", "desc")
					    ->find_all();

		return $result;
	}

	public function get_header()
	{
		$category = $this->_category;
		$user_id  = $this->_user_id;
		$config = Kohana::$config->load('massload/bycategory.'.$category);
		$header = array();
		foreach ($config["fields"] as $name => $fieldset) {
			$header[] = $fieldset["translate"].(($fieldset["required"])?"*":"");
		}

		return $header;
	}

	public function row($object)
	{
		$category = $this->_category;
		$config = Kohana::$config->load('massload/bycategory.'.$category);

		$object = $this->set_external_id($object);
		$row = $this->get_row($object);
		$row = $this->convert_values_in_row($row);

		$result_row = array();
		foreach ($config["fields"] as $name => $fieldset) {
			 $result_row[] = $row[$name];
		}
		return $result_row;
	}

	public function convert_values_in_row(array $row)
	{
		$_row = $row;
		$category = $this->_category;
		$user_id  = $this->_user_id;

		$conformities = ORM::factory('User_Conformities')
							->where("user_id","=", $user_id)
							->where("massload","=", $category)
							->find_all();
		foreach ($conformities as $conformity) {
			if ($conformity->value == $_row[$conformity->type])
				$_row[$conformity->type] = $conformity->conformity;
			
		}

		foreach ($_row as $key => $value) {
			$_row[$key] = str_replace ( chr(146), "'", $value );
		}

		return $_row;
	}

	public function set_external_id($object)
	{
		$_object = $object;
		if (!$_object->number)
		{
			ORM::factory('Object', $_object->id)
				->set("number", $_object->id)
				->update();

			$_object->number = $_object->id;
		}

		return $_object;
	}

	public function get_row($object)
	{
		$category = $this->_category;
		$config = Kohana::$config->load('massload/bycategory.'.$category);
		$domain = "http://".Kohana::$config->load('common.main_domain');

		$reserved = array("external_id", "city", "address",
								"user_text_adv","contact_0_value", "contact_1_value", "contact", "images");

		$row = array();
		$row["external_id"] 	= $object->number;
		$row["city"] 			= ORM::factory('City', $object->city_id)->title;
		$row["address"] 		= ORM::factory('Location', $object->location_id)->address;
		$row["title"] 			= $object->title;
		$row["user_text_adv"] 	= $object->user_text;
		$row["contact"] 	  	= $object->contact;

		$row["contact_0_value"] = ORM::factory('Object_Contact')
										->where("object_id", "=",$object->id)
										->order_by("id","desc")
										->find()->contact_obj->contact_clear;

		$row["contact_1_value"] =  ORM::factory('Object_Contact')
										->where("object_id", "=",$object->id)
										->order_by("id","desc")
										->offset(1)
										->find()->contact_obj->contact_clear;

		/* images */
		$images = array();
		$_images = ORM::factory('Object_Attachment')
						->where("object_id", "=",$object->id)
						->find_all();
		foreach ($_images as $image) 
			if (!$image->url)
				$images[] = $domain.Imageci::getOriginalSitePath($image->filename);
			else
				$images[] = $image->url;

		$row["images"] 			=  implode(";", $images);
		/* images */

		$_attributes = array_diff(array_keys($config['fields']), $reserved);
		$attributes = ORM::factory('Object')->get_attributes($object->id);
		foreach ($attributes as $attribute) {
			$attribute = new Obj($attribute);
			$value = NULL;
			if  (in_array($attribute->seotitle, $_attributes))
			{
				if ($attribute->types == "list" OR $attribute->types == "text")
					$value = $attribute->tvalue;
				elseif ($attribute->types == "numeric")
					$value = round($attribute->min_value, 2);
				else
					$value = $attribute->min_value;

				$row[$attribute->seotitle] = $value;
			}


		}

		return $row;
	}

	public function get_excel_file($data)
	{
		$category = $this->_category;
		$config = Kohana::$config->load('massload/bycategory.'.$category);

		if (!$config)
			return;

		$spreadsheet = Spreadsheet::factory(array(
			      'author'  => 'yarmarka',
			      'path' => 'uploads/',
			      'name' => $config["name"],
			      'format' => 'Excel5'
			));

		$spreadsheet->set_active_worksheet(0);
		$as = $spreadsheet->get_active_worksheet();
		$as->title('Объявления');
		$spreadsheet->set_data($data);
		$spreadsheet->send();
	}
}