<?php defined('SYSPATH') OR die('No direct script access.');

class Assets extends Kohana_Assets {

	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (\Exception $e)
		{
			echo Debug::vars($e->getMessage());
		}
	}
}