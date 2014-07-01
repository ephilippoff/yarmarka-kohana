<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Tmp_Img extends ORM
{
	protected $_table_name = 'tmp_img';
	protected $_created_column  = array('column' => 'added', 'format' => 'Y-m-d H:i:s');

	public function delete_by_name($filename)
	{
		DB::delete($this->_table_name)
			->where('name', '=', $filename)
			->execute($this->_db);

		return $this;
	}
}

/* End of file Img.php */
/* Location: ./application/classes/Model/Tmp/Img.php */