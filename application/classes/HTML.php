<?php defined('SYSPATH') OR die('No direct script access.');

class HTML extends Kohana_HTML {
	
	public static function render_menu($categories, $parent_id = 0)
	{
		$array = array();
		
		function print_menu($array, $parent_id = 0)
		{
			if (!isset($array[$parent_id])) return false;

			foreach ($array[$parent_id] as $value)
			{
				echo '<li>';
				echo '<a href="/'.Route::get('article')->uri(array('seo_name' => $value['seo_name'])).'">'.$value['title'].'</a>';				

				if (isset($array[$value['id']]))
				{
					echo '<ul>';
					print_menu($array, $value['id']);
					echo '</ul>';
				}
				
				echo '</li>';                        
			}

		} 
		
		foreach ($categories as $category) 		
			$array[$category->parent_id][] = $category->as_array();
			
		echo '<ul id="navigation">';
		print_menu($array, $parent_id);
		echo '</ul>';
	}
}