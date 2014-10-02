<?php defined('SYSPATH') or die('No direct script access.');


class Task_Test extends Minion_Task
{
	protected $_options = array(

	);

	protected function _execute(array $params)
	{
		$name = Temptable::get_name(array("flat_new", 327190));

		Temptable::create_table($name, array(
					0 => array(
							"name" => "text",
							"type" => "textadv"
						),
					1 => array(
							"name" => "title",
							"type" => "dict"
						)

				));

		$t = ORM_Temp::factory($name);

		$t->text = 'sdfsdf';
		$t->title = 'dd';
		$id = $t->save();

		Minion_CLI::write('id: '.$id);

		$tt = ORM_Temp::factory($name, $id);
		Minion_CLI::write('title: '.$tt->title);

		//Temptable::delete_table($name);
	}

}
