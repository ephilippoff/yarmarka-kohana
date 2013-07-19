<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Service_Invoices 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Service_Invoices extends ORM {

	protected $_table_name = 'service_invoices';

	protected $_belongs_to = array(
		'invoice' 	=> array(),
		'object'	=> array(),
	);

} // End Service_Invoices Model
