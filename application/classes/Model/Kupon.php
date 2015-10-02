<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Kupon extends ORM
{
	protected $_table_name = 'kupon';

	protected $_has_many = array(
	);

	protected $_belongs_to = array(
		'invoice'	=> array('model' => 'Invoices', 'foreign_key' => 'invoice_id')
	);
	
	public function with_objects()
	{
		return $this->select(array('object.title', 'object_title'))
			->join('object', 'left')
			->on('kupon.object_id', '=', 'object.id');
	}	
	
	public function with_invoices()
	{
		return $this->select(array('invoices.user_id', 'user_id'))
			->join('invoices', 'left')
			->on('kupon.invoice_id', '=', 'invoices.id');
	}
	
	public function sum_by_field($field)
	{
		$query = DB::select('object_id', DB::expr('SUM('.$field.')'))
				->from('kupon')
				->group_by('object_id')
				->as_object()
				->execute();
		
		return $query;				
	}
}