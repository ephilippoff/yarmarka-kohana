<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Category_Banners_Stats extends ORM {

	protected $_table_name = 'category_banners_stats';
	
	function increase_visits($id)
	{
		if (!$id = (int)$id) return 0;
		
		$stat = ORM::factory('Category_Banners_Stats')
				->where('banner_id', '=', $id)
				->where('date', '=', DB::expr('CURRENT_DATE'))
				->find();
		
		if ($stat->loaded())
		{
			$stat->clicks_count++;
			$stat->save();
		}
		else
		{
			$stat = ORM::factory('Category_Banners_Stats');
			$stat->banner_id = $id;
			$stat->clicks_count = 1;
			$stat->date = DB::expr('CURRENT_DATE');
			$stat->save();
		}
							
		return;				
	}	
	
}