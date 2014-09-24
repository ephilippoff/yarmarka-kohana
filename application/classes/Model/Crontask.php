<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Crontask extends ORM {

	protected $_table_name = 'crontask';


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
		$this->state = 3;
		$this->comment = $errorText;
		$this->update();

		return $this;
	}

	function _update()
	{
		if (!$this->loaded())
			return;
		$this->updated_on = 'NOW()';
		$this->update();

		return $this;
	}

	function end()
	{
		if (!$this->loaded())
			return;
		if ($this->state <2) {
			$this->state = 2;
			$this->update();
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
