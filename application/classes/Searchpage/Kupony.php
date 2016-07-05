<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage_Kupony extends Searchpage_Default
{

    public function __construct()
    {
        
    }

    public function get_twig_data($search_info, $request, $cached)
    {
        

        if ($search_info->city_id) {
            Cookie::set('location_city_id', $search_info->city_id, strtotime('+31 days'));
        }
        
        $search_params = Search_Url::clean_reserved_query_params($request->query());
        
        $twig            = Twig::factory('search/kupony/index');

        $twig->onPageFlag = 'search';

        $search_info->search_filters['expiration'] = true;

        
        //main search
        $main_search_query = Search::searchquery($search_info->search_filters, $search_params);
        
        $twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());
        
        if (!$search_info->main_search_result_count) {
            $main_search_result_count              = Search::searchquery($search_info->search_filters, array(), array(
                "count" => TRUE
            ))->execute()->get("count");
            $search_info->main_search_result_count = (int) $main_search_result_count;
        }

        //end main search
        
        //premium
        $premium_search_query        = Search::searchquery(array_merge($search_info->search_filters, array(
            "premium" => TRUE,
            "not_category_seo_name" => array(
                "novosti"
            )
        )), array_merge($search_params, array(
            "limit" => 5
        )));
        $twig->premium_search_result = Search::getresult($premium_search_query->execute()->as_array());
        foreach ($twig->premium_search_result as $key => $value) {
            $twig->premium_search_result[$key]["is_premium"] = TRUE;
        }
        if (count($twig->premium_search_result) > 0) {
            $premium_search_coords = array_map(function($item)
            {
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "type" => "premium",
                    "price" => $item["price"],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(
                        @$item["compiled"]["lat"],
                        @$item["compiled"]["lon"]
                    )
                );
            }, $twig->premium_search_result);
        }
        //premium end
        
        //vip
        $vip_search_query        = Search::searchquery(array(
            "search_text" => @$search_info->search_filters["search_text"],
            "photocard" => TRUE,
            "active" => TRUE,
            "published" => TRUE,
            "city_id" => $search_info->city_id,
            "category_id" => (count($search_info->child_categories_ids) > 0) ? $search_info->child_categories_ids : $search_info->category->id
        ), array_merge($search_params, array(
            "limit" => 15,
            "page" => 1
        )));
        $twig->vip_search_result = Search::getresult($vip_search_query->execute()->as_array());
        shuffle($twig->vip_search_result);
        if (count($twig->vip_search_result) > 0) {
            $vip_search_coords = array_map(function($item)
            {
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "type" => "lider",
                    "price" => $item["price"],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(
                        @$item["compiled"]["lat"],
                        @$item["compiled"]["lon"]
                    )
                );
            }, $twig->vip_search_result);

        }
        //vip end

      
        
        //pagination
        $pagination = Pagination::factory(array(
            'current_page' => array(
                'source' => 'query_string',
                'key' => 'page'
            ),
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_params['limit'],
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'path' => isset($GLOBALS['category_path']) ? $GLOBALS['category_path'] : URL::SERVER("PATH_INFO"),
            'count_out' => 0,
            'count_in' => 4,
            'limits' => array(
                "25" => Search_Url::get_suri_without_reserved($request->query(), array(), array(
                    "limit",
                    "page"
                )),
                "50" => Search_Url::get_suri_without_reserved($request->query(), array(
                    "limit" => 50
                ), array(
                    "page"
                )),
                "75" => Search_Url::get_suri_without_reserved($request->query(), array(
                    "limit" => 75
                ), array(
                    "page"
                ))
            )
        ));

        $twig->pagination = $pagination;
        //pagination end
        

        //save search settings cache
        if (!$cached) {
            $cache = $this->save_search_info_to_cache($request->query(), array(
                "info" => $search_info,
                "canonical_url" => $search_info->canonical_url,
                "sql" => (string) $main_search_query,
                "count" => $search_info->main_search_result_count
            ));
            Cookie::set('search_hash', $cache->hash, strtotime('+14 days'));
            
        }
        //save search settings cache end
        
        if ($search_info->s_suri <> "/" . $search_info->canonical_url) {
            $search_info->show_canonical = TRUE;
            $search_info->is_canonical   = FALSE;
        } else {
            $search_info->is_canonical = TRUE;
        }
        if ($search_info->search_text) {
            $search_info->show_canonical = FALSE;
            $search_info->is_canonical   = FALSE;
        }
        
        $iCurrentPage = (int) $request->query('page');
        
        //https://support.google.com/webmasters/answer/139066?hl=ru#2
        //if ($iCurrentPage > 1) {
        $search_info->show_canonical        = true;
        $bIsHttps                           = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
        $sCanonicalUrlPrefix                = 'http' . ($bIsHttps ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $search_info->full_canonical_url    = $sCanonicalUrlPrefix . $search_info->canonical_url;
        $search_info->show_canonical_simple = true;
        //}
        
        $twig->banner_zone_positions = Kohana::$config->load('common.banner_zone_positions');
        
        
        foreach ((array) $search_info as $key => $item) {
            $twig->{$key} = $item;
        }
        $twig->isGuest = Auth::instance()->get_user() == NULL;
        
        
        $twig->staticMainMenu = TRUE;
        
        $twig->isNews            = $search_info->category->id == 174;
        $twig->isNewsSubcategory = array_key_exists('filters', $search_info->search_filters) && is_array($search_info->search_filters['filters']) && array_key_exists('news-category', $search_info->search_filters['filters']) && $search_info->search_filters['filters']['news-category'] != NULL;
        
        if ($twig->isNewsSubcategory) {
            $twig->catTitle = $search_info->search_filters['filters']['news-category'];
        }
        
        return $twig;
        
    }

}