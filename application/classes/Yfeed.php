<?php if ( ! defined('SYSPATH')) exit('No direct script access allowed');

/**
 * Класс для преобразования атрибутов объявления 
 * в структуру YML для яндекс
 */
class Yfeed
{
	public $data = array();
	public $dom = NULL;
	public $root = NULL;

	function __construct($root_name)
	{

		$dom = new DOMDocument("1.0", "utf-8");
		$source = $dom->createElement("source"); // Создаём корневой элемент
		$source->setAttribute("creation-time", date("Y-m-d H:i:s")." GMT+5");
		$source->setAttribute("host", "http://yarmarka.biz");
		$dom->appendChild($source);

		$root = $dom->createElement($root_name);
		$source->appendChild($root);

		$this->dom = $dom;
		$this->root = $root;
		$this->data = array();
	}

	function single($name, $title)
	{
		return $this->data[$name] = $title;
	}

	function multiple($name, array $singles, $parent = FALSE)
	{
		$result = array();

		foreach ($singles as $key => $value) {
			if (is_array($value))
				$result[$key] = self::multiple($key, $value, TRUE);
			else
				$result[$key] = $value;
		}
		if (!$parent)
			$this->data[$name] = $result;
		
		return  $result;
	}

	function compile_row(&$dom, &$parent_node, $name, $row)
	{

		if (is_array($row))
		{
			$element = $dom->createElement($name);
			$parent_node->appendChild($element);
			foreach ($row as $subname => $subrow) {				
				$this->compile_row($dom, $element, $subname, $subrow);
			}
		} else {
			$element = $parent_node->appendChild( $dom->createElement($name));
			$element->appendChild($dom->createTextNode($row));
			
		}
	}

	function compile($name = "vacancy")
	{
		$vacancy = $this->dom->createElement($name);
		$this->root->appendChild($vacancy);
		foreach ($this->data as $name => $row) {	
			$this->compile_row($this->dom, $vacancy, $name, $row);
		}
	}

	function reset()
	{
		$this->data = array();
	}

	function save()
	{
		return $this->dom->saveXML();
	}
}