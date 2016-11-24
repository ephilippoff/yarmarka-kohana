<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage_News extends Searchpage_Default
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
        
        $twig            = Twig::factory('search/news/index');

        
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

        //premium news
        $search_query      = Search::searchquery(array(
            "expiration" => TRUE,
            "premium" => TRUE,
            "active" => TRUE,
            "published" => TRUE,
            "city_id" => $search_info->city_id,
            "category_seo_name" => "novosti"
        ), array(
            "limit" => 4,
            "page" => 1
        ));
        $twig->premiumnews = Search::getresult($search_query->execute()->as_array());

        $premium_ids = array_map(function($item)
        {
            return $item["id"];
        }, $twig->premiumnews);
        //premium news end

        //lastnews
        $search_query   = Search::searchquery(array(
            "expiration" => TRUE,
            "active" => TRUE,
            "published" => TRUE,
            "city_id" => $search_info->city_id,
            "category_seo_name" => "novosti"
            
        ), array(
            "limit" => 7,
            "page" => 1,
            "order" => "date_expired"
        ));
        $twig->lastnews = Search::getresult($search_query->execute()->as_array());
        //lastnews end
        
        // //kupons
        // $premium_kupons = Search::searchquery(array(
        //     "active" => TRUE,
        //     "published" => TRUE,
        //     "expiration" => TRUE,
        //     "premium" => TRUE,
        //     "category_id" => array(
        //         173
        //     ),
        //     "city_id" => ($search_info->city_id) ? array(
        //         $search_info->city_id
        //     ) : NULL
        // ), array(
        //     "limit" => 3,
        //     "order" => "date_expired"
        // ));
        
        // $twig->premium_kupons = Search::getresult($premium_kupons->execute()->as_array());
        
        // $kupons = Search::searchquery(array(
        //     "active" => TRUE,
        //     "published" => TRUE,
        //     "expiration" => TRUE,
        //     "category_id" => array(
        //         173
        //     ),
        //     "city_id" => ($search_info->city_id) ? array(
        //         $search_info->city_id
        //     ) : NULL
        // ), array(
        //     "limit" => 3,
        //     "order" => "date_expired"
        // ));
        
        // $twig->kupons = Search::getresult($kupons->execute()->as_array());
        // //kupons end
        

        
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