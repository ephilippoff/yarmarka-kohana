<?php defined('SYSPATH') or die('No direct script access.');

class Massload_Avito
{

	public function convert_file($filepath)
	{
		$return = Array();
		$f = new Massload_FileXml();
		$filenames = Array();
		$files = new Obj();

		$f->forAll($filepath, function ($row, $i) use ($filepath, &$filenames, &$files){
			$category = Massload_Avito::get_category($row);

			if (!$category)  return "continue";

			if (!array_key_exists($category, $filenames))
			{
				$dom = new DOMDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
				$root = $dom->createElement("Ads"); // Создаём корневой элемент
				$dom->appendChild($root);

				$filenames[$category] = $filepath.$category;
				$files->{$category} = $dom;
				
			} else {
				$dom = $files->{$category};
				$root = $dom->documentElement;
			}


			$images = Array();
			if (property_exists($row, "Images")){
				foreach ($row->Images->Image as $image)
				{
					$attributes = $image->attributes();
					$images[] = (string) $attributes["url"][0];
				}
			}

			$row = new Obj((array) $row);
			unset($row->Images);
			$row->Images = $images;
			$converted_row = Massload_Avito::convert_avito_row($category, $row);

			$converted_row = Massload_Avito::format_values($converted_row);

			$item = Massload_Avito::generate_valid_xml_from_array($converted_row, "Ad", "Image");


			$tpl = new DOMDocument;
			$tpl->loadXml($item);
			$root->appendChild($dom->importNode($tpl->documentElement, TRUE));

		});
		$return = Array();
		foreach($files as $key=>$value)
		{
			$dirname = pathinfo($filepath, PATHINFO_DIRNAME);
			$extension = (pathinfo($filepath, PATHINFO_EXTENSION)) ? ".".pathinfo($filepath, PATHINFO_EXTENSION):"";
			$filename = pathinfo($filepath, PATHINFO_FILENAME);

			$new_filename = $key."_".$filename;
			$new_file_path = $dirname."/".$new_filename.$extension;
			$save = $value->save($new_file_path);		
			if ($save)
				$return[$key] = $new_file_path;
		}
		return $return;
	}

	public static function get_category($row)
	{
		$return = FALSE;
		$category_conformities = Kohana::$config->load('massload/avito_categories');
		foreach($category_conformities as $category=>$conformity)
		{
			$valid = 0;
			foreach ($conformity as $field=>$value)
			{				
				if ($row->{$field} == $conformity[$field]) 
					$valid++;

				if ($valid == count($conformity)){
					$return = $category;
					break;
				}

				
			}
		}
		return $return;
	}

	public static function convert_avito_row($category, $row)
	{
		$new_row = new Obj();
		$fields_conformity = Kohana::$config->load('massload/avito_fields.'.$category);
		foreach ($fields_conformity as $field=>$conformity)
		{
			$new_row->{$conformity} = $row->$field;
		}
		return $new_row;
	}

	public static function generate_xml_from_array($array, $node_name) {
		$xml = '';
		if (is_array($array) || is_object($array)) {
			foreach ($array as $key=>$value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}

				if ($key == "Image")
				{
					$xml .= '<' . $key . ' url="' . self::generate_xml_from_array($value, $node_name) . '"/>' . "\n";
				} else {
					$xml .= '<' . $key . '>' . self::generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
				}
			}
		} else {
			$xml = htmlspecialchars($array, ENT_QUOTES) ;
		}

		return $xml;
	}

	public static function generate_valid_xml_from_array($array, $node_block='nodes', $node_name='node') {
		$xml = "";

		$xml .= '<' . $node_block . '>' . "\n";
		$xml .= self::generate_xml_from_array($array, $node_name);
		$xml .= '</' . $node_block . '>' . "\n";

		return $xml;
	}

	public static function format_values($row)
	{
		$row = self::clear_row($row);
		$row = self::format_contacts($row);
		return $row;
	}

	public static function clear_row($row)
	{
		foreach ($row as $key=>$value)
		{ 
			if ($key <> "images")
				$row->{$key} = strip_tags($value);
		}
		return $row;
	}

	public static function format_contacts($row)
	{
		$user = Auth::instance()->get_user();
		$contacts = ORM::factory('User_Contact')
						->where("user_id","=", $user->id)
						->limit(2)
						->cached(Date::HOUR)
						->find_all();

		if (!$row->contact_0_value) {
			$i = 0;
			foreach($contacts as $contact){
				$row->{"contact_".$i."_value"} = $contact->contact->contact_clear;
				$i++;
			}
		}

		$num_prefixes = array("+7","8");

		if (in_array(substr($row->contact_0_value, 0,1), $num_prefixes) and !Valid::email($row->contact_0_value))
			$row->contact_0_value =  substr_replace($row->contact_0_value, "7", 0, 1);

		if (in_array(substr($row->contact_1_value, 0,1), $num_prefixes) and !Valid::email($row->contact_1_value))
			$row->contact_1_value =  substr_replace($row->contact_1_value, "7", 0, 1);
	

		if (!$row->contact)
			$row->contact = $user->org_name;

		return $row;
	}

}