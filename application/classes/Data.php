<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Data extends ORM {
	
	public function is_range_value()
	{
		$type = strtolower(str_replace('Model_Data_', '', get_class($this)));

		switch ($type) {
			case 'numeric':
			case 'integer':
			case 'date':
				$result = TRUE;
			break;
			
			default:
				$result = FALSE;
			break;
		}

		return $result;
	}
}

/* End of file Data.php */
/* Location: ./application/classes/Model/Data.php */