<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Object_Service_Ticket extends ORM {

	protected $_table_name = 'object_service_ticket';	
	
	protected $_belongs_to = array(
		'object_obj'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
		'invoice_obj'	=> array('model' => 'Invoices', 'foreign_key' => 'invoice_id'),
	);		
}