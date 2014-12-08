<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Priceload_Index extends ORM {

	protected $_table_name = 'priceload_index';


	function search($attributes)
	{
		$pi = $this;

		if ($attributes)
			foreach ($attributes as $attribute=>$value) {
	            if ($value AND $value<>"")
	            	$pi = $pi->where(DB::expr("1"),"=", DB::expr("
	                            (SELECT 1 FROM priceload_idata
	                            WHERE priceload_index_id = priceload_index.id
	                            AND priceload_attribute_id = ".$attribute."
	                            AND priceload_filter_id = ".$value." LIMIT 1)"));

	        }

		return $pi;
	}
} 
