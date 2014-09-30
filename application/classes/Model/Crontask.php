<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Crontask extends ORM {

	protected $_table_name = 'crontask';

	function get_states()
	{
		return array(
				0 => "В очереди",
				1 => "Выполняется",
				2 => "Завершена",
				3 => "Ошибка",
				4 => "Остановлена",
				5 => "В архиве"
			);
	}

	function get_command($name, $params = array(), $aps = FALSE)
	{
		$command = 'php index.php --task='.$name.' ';

		$_params = array();
		foreach($params as $key=>$param)
			if ($param)
				$_params[] = "--".$key."=".$param;

		$command .= implode(" ", $_params);

		if ($aps)
			$command .= " &";

		return $command;
	}

	function begin($name, $params = array())
	{
		$command = $this->get_command($name, $params);

		$this->name = $name;
		$this->command = $command;
		$this->state = 1;
		$this->updated_on = 'NOW()';
		$this->save();

		return $this;
	}

	function error($errorText = '')
	{
		if (!$this->loaded())
			return;

		$ct = ORM::factory('Crontask', $this->id);
		if (!$ct->loaded())
			return;

		$ct->state = 3;
		$ct->comment = $errorText;
		$ct->update();

		return $this;
	}

	function _update()
	{
		if (!$this->loaded())
			return;

		$ct = ORM::factory('Crontask', $this->id);
		if (!$ct->loaded())
			return;

		$ct->updated_on = 'NOW()';
		$ct->update();

		return $this;
	}

	function end()
	{
		if (!$this->loaded())
			return;

		$ct = ORM::factory('Crontask', $this->id);
		if (!$ct->loaded())
			return;
		
		if ($ct->state < 2) {
			$ct->state = 2;
			$ct->update();
		}

		$this->to_archive();

		return $this;
	}

	function _check($id)
	{
		$ct = ORM::factory('Crontask', $id);

		if (!$ct->loaded())
			return FALSE;
		elseif ($ct->state == 4 OR $ct->state == 5)
			return FALSE;
		else 
			return TRUE;
	}

	function to_archive()
	{
		ORM::factory('Crontask')
			->where("created_on","<=",DB::expr("CURRENT_DATE - interval '7 days'"))
			->set("state", 5)
			->update_all();
	}

} // End Crontask Model
