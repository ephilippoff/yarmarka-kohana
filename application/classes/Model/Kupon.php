<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Kupon extends ORM
{
	protected $_table_name = 'kupon';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'invoice'	=> array('model' => 'Invoices', 'foreign_key' => 'invoice_id')
	);
}