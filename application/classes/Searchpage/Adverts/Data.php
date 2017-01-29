<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage_Adverts_Data
{
	static public function get_other_adverts($search_info) {

		$result = self::find_other_adverts($search_info);
		while (count($result) == 0) {
		    
		    $newSearchText = explode(' ', $search_info->search_text);
		    if (count($newSearchText) > 1) {
		        $search_info->search_text = array_shift($newSearchText);
		    } elseif (count($newSearchText) == 1) {
		        $newSearchText = implode('', $newSearchText);
		        if (strlen($newSearchText) > 3) {
		            $search_info->search_text = substr($newSearchText, 0, -2);
		        } else
		            break;
		    }
		    
		    $result = self::find_other_adverts($search_info);
		}
		
		return $result;

	}

	static public function find_other_adverts($search_info)
	{
	    
	    // $categoryID = ($search_info->category->id == 1) ? $search_info->child_categories_ids : $search_info->category->id;
	    
	    $filters = array(
	        "active" => TRUE,
	        'expiration' => true,
	        'expired' => true,
	        'published' => true,
	        "city_id" => $search_info->city->id,
	        "search_text" => $search_info->search_text
	        // "category_id" => $categoryID
	    );
	    
	    
	    $category = $search_info->category;
	    
	    while (1 == 1) {
	        $result = Search::getresult(Search::searchquery($filters, array(
	            "limit" => 50,
	            "page" => 1
	        ))->execute()->as_array());
	        
	        if (count($result) > 0 OR !$category->parent_id OR $category->id == 1) {
	            break;
	        }
	        
	        $category               = ORM::factory('Category', $category->parent_id);
	        $filters['category_id'] = $category->id;
	    }
	    
	    // foreach ($result as $key => $value) {
	    //     if (count($result[$key]['compiled']) == 0) {
	    //         unset($result[$key]);
	    //     }        
	    // }
	    
	    
	    if (shuffle($result)) {
	        return $result;
	    }
	}
}