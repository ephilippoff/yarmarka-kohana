<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * HTML helper class. Provides generic methods for generating various HTML
 * tags and making output HTML safe.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
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

} // End html
