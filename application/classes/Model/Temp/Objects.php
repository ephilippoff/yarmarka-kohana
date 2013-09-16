<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Data_Boolean extends ORM
{
	protected $_table_name = 'temp_objects';

	protected $_created_column = array('column' => 'date_status_change', 'format' => 'Y-m-d H:i:s');
}

/* End of file Objects.php */
/* Location: ./application/classes/Model/Temp/Objects.php */