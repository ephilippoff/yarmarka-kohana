<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage_Adverts extends Searchpage_Default
{

    public function __construct()
    {
        
    }

    public function get_twig_data($search_info, $request, $cached)
    {
        
        $objects_for_map = array();
        $prefix          = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
        $staticfile      = new StaticFile("attributes", $prefix . 'static_attributes.js');

        if ($search_info->city_id) {
            Cookie::set('location_city_id', $search_info->city_id, strtotime('+31 days'));
        }
        
        $search_params = Search_Url::clean_reserved_query_params($request->query());
        
        $twig            = Twig::factory('search/index');
        $twig->data_file = $staticfile->jspath;
        
        $twig->onPageFlag = 'search';
        $twig->itemscope  = 'itemscope itemtype="http://schema.org/SearchResultsPage"';
        
        /*
        http://yarmarka.myjetbrains.com/youtrack/issue/yarmarka-347
        http://yarmarka.myjetbrains.com/youtrack/issue/yarmarka-352
        */
        //$newsCategoryId = 174;
        //if (in_array($search_info->category->id, $categoriesToFilterExpiration)) {
        $search_info->search_filters['expiration'] = true;
        //}
        /*
        done
        */
        
        //main search
        $main_search_query = Search::searchquery($search_info->search_filters, $search_params);
        
        $twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());
        
        if (!$search_info->main_search_result_count) {
            $main_search_result_count              = Search::searchquery($search_info->search_filters, array(), array(
                "count" => TRUE
            ))->execute()->get("count");
            $search_info->main_search_result_count = (int) $main_search_result_count;
        }

        if (count($twig->main_search_result) > 0) {
            $main_search_coords = array_map(function($item)
            {
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "price" => $item['price'],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(
                        @$item["compiled"]["lat"],
                        @$item["compiled"]["lon"]
                    )
                );
            }, $twig->main_search_result);
            
            $objects_for_map = array_merge($objects_for_map, $main_search_coords);
            
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
            
            $objects_for_map = array_merge($objects_for_map, $premium_search_coords);
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
            
            $objects_for_map = array_merge($objects_for_map, $vip_search_coords);
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
        $twig->small_pagination = $pagination;
    
        
        $limitList = Pagination::factory(array(
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_params['limit'],
            'view' => 'pagination/limit',
            'path' => URL::SERVER("PATH_INFO"),
            'limits' => array(
                "30" => Search_Url::get_suri_without_reserved($request->query(), array(), array(
                    "limit",
                    "page"
                )),
                "40" => Search_Url::get_suri_without_reserved($request->query(), array(
                    "limit" => 40
                ), array(
                    "page"
                )),
                "50" => Search_Url::get_suri_without_reserved($request->query(), array(
                    "limit" => 50
                ), array(
                    "page"
                ))
            )
        ));
        
        $twig->limitList = $limitList;
        //pagination end
        
        
        //save search settings cache
        if (!$cached AND !$search_info->search_text) {
            $cache = $this->save_search_info_to_cache($request->query(), array(
                "info" => $search_info,
                "canonical_url" => $search_info->canonical_url,
                "sql" => (string) $main_search_query,
                "count" => $search_info->main_search_result_count
            ));
            Cookie::set('search_hash', $cache->hash, strtotime('+14 days'));
            
        }
        //save search settings cache end
        
        //favourites
        $twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
        //end favourites
        
        if ($search_info->category->show_map or count($twig->vip_search_result) > 6) {
            if (count($objects_for_map) > 0) {
                $twig->objects_for_map = json_encode($objects_for_map);
                $twig->set_filename('search/index/with_map');
            }
        }
        
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
        
        if ($search_info->category->seo_name == 'kupony') {
            $twig->set_filename('search/kupony/index');
        } else if ($search_info->category->seo_name == 'novosti') {
            $twig->set_filename('search/news/index');
        }
        
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
        
        $this->cache_stat($twig, $search_params);
        
        // $search_links = new Search_Links($search_info);
        // $search_links->get_count();

        // if (property_exists($twig, 'category_childs_elements')) {
        //     foreach ($twig->category_childs_elements as &$value) {
        //         $k = $twig->s_host . '/' . $twig->category_url . '/' . $value->url;
        //         if (!array_key_exists($k, $twig->link_counters)) {
        //             $value->count = 0;
        //         } else {
        //             $value->count = $twig->link_counters[$k];
        //         }
        //     }
        //     $this->process_child_categories($twig->category_childs_elements);
        // }
        
        // if (property_exists($twig, 'category_childs')) {
        //     foreach ($twig->category_childs as &$value) {
        //         $k = $twig->s_host . '/' . $value->url;
        //         if (!array_key_exists($k, $twig->link_counters)) {
        //             $value->count = 0;
        //         } else {
        //             $value->count = $twig->link_counters[$k];
        //         }
        //     }
        //     $this->process_child_categories($twig->category_childs);
        // }
        
        if (count($twig->main_search_result) == 0) {
            $result = $this->find_other_adverts($search_info);
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
                
                $result = $this->find_other_adverts($search_info);
            }
            
            $twig->other_adverts = $result;
        }
        
        return $twig;
        
    }

    public function get_search_info_by_sphinx($search_text,  Search_Url $search_url, Domain $domain, Request $request)
    {
        $clean_query_params = array_merge($search_url->get_clean_query_params(), $search_url->get_seo_filters());
        $info               = new Obj();
        
        $info->search_text = $search_text;
        
        $info->city_id              = ($domain->get_city()) ? $domain->get_city()->id : NULL;
        if ($info->city_id === 1) {
            $info->city_id = NULL;
        }
        $info->category_id          = $search_url->get_category()->id;
        $info->child_categories_ids = $search_url->get_category_childs_id();
        $info->category_deep_childs = array_map(function($a)
        {
            return $a['id'];
        }, Services_Factory::instance('Categories')->getCategoryWithChilds($info->category_id, 8));
        
        $info->s_host        = URL::SERVER("HTTP_HOST");
        $info->s_suri        = trim(URL::SERVER("REQUEST_URI"), "/");
        $info->domain        = $domain;
        $info->city          = $domain->get_city();
        $info->main_category = $domain->get_main_category();
        
        $sphinx                 = new Sphinx();
        $sphinx_category_childs = $sphinx->searchGroupByCategory($info->search_text, $info->city_id, (count($info->child_categories_ids) > 0) ? $info->child_categories_ids : $info->category_id);
        
        $info->sphinx_category_childs           = $info->category_childs = $sphinx_category_childs["categories"];
        $info->category_childs_elements         = $search_url->get_category_childs_elements($info->category_id, $info->city_id, $search_url->get_seo_filters());
        $info->category_childs_elements_colsize = 4;
        
        $info->category_url        = $search_url->get_proper_category_uri();
        $info->url                 = $info->s_host . "/" . $info->category_url;
        $info->canonical_url       = $search_url->get_proper_segments();
        $info->sphinx_search_query = "?search=" . $info->search_text;
        $info->dirty_url           = $info->url . $info->sphinx_search_query;
        if ($info->canonical_url === $info->main_category) {
            $info->canonical_url = "";
        }
        if ($info->s_suri <> "/" . $info->canonical_url . $info->sphinx_search_query) {
            $info->show_canonical = TRUE;
        }
        $info->category                         = $search_url->get_category();
        $info->crumbs                           = $search_url->get_category_crubms($info->category_id, $info->sphinx_search_query);
        $info->incorrectly_query_params_for_seo = $search_url->incorrectly_query_params_for_seo;
        $info->search_filters                   = array(
            "active" => TRUE,
            "published" => TRUE,
            "city_id" => $info->city_id,
            "category_id" => (count($info->child_categories_ids) > 0) ? $info->category_deep_childs : $info->category_id,
            
            "user_id" => $search_url->get_reserved_query_params("user_id"),
            "source" => $search_url->get_reserved_query_params("source"),
            "photo" => $search_url->get_reserved_query_params("photo"),
            "video" => $search_url->get_reserved_query_params("video"),
            "private" => $search_url->get_reserved_query_params("private"),
            "org" => $search_url->get_reserved_query_params("org"),
            "filters" => $clean_query_params,
            
            //TODO фильтр по фото
            //"user_id" => $search_url->get_reserved_query_params("user_id"),
            //"photo" => $search_url->get_reserved_query_params("photo"),
            
            "search_text" => $info->search_text
            //"filters" => array()
        );
        
        $info->seo_attributes = Seo::get_seo_attributes($search_url->get_proper_segments(), $info->search_filters["filters"], $search_url->get_category(), $domain->get_city());
    
        $info->seo_attributes['h1'] =  sprintf('Поиск объявлений "%s" в %s', $info->search_text, $domain->get_city()->sinonim);
        $info->seo_attributes['title'] =  sprintf('"%s" в %s.', $info->search_text, $domain->get_city()->sinonim);


        $info->query_params_for_js = json_encode(array_merge($search_url->get_query_params_without_reserved($request->query()), $clean_query_params));
        
        
        return $info;
    }

    public function find_other_adverts($search_info)
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

    protected function process_child_categories(&$arr)
    {
        usort($arr, function($a, $b)
        {
            
            //1. by count desc
            $count_a = (int) $a->count;
            $count_b = (int) $b->count;
            
            if ($count_a != $count_b) {
                return $count_b - $count_a;
            }
            
            //2. by title
            $title_a = $a->title;
            $title_b = $b->title;
            
            if ($title_a != $title_b) {
                return $title_a < $title_b ? -1 : 1;
            }
            
            //3. by weight
            $weight_a = (int) $a->weight;
            $weight_b = (int) $b->weight;
            
            return $weight_b - $weight_a;
            
        });
        return $arr;
    }

}