<?php

class Model_Order_Log extends ORM
{
	protected $_table_name = 'order_log';


	function write($id, $type, $description) {

		$this->identifier = $id;
		$this->type = $type ? $type : 'unknown';
		$this->description = 'surgut.yarmarka.biz: '.$description;
		$this->save();
	}
}