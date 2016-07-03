<?php defined('SYSPATH') or die('No direct script access.');

class Searchpage_Default
{
	protected $_search_info = NULL;

	public function __construct($search_info)
	{
		
	}

	static public function get_search_info( Search_Url $search_url, Domain $domain, Request $request )
    {
        $clean_query_params = array_merge($search_url->get_clean_query_params(), $search_url->get_seo_filters());
        
        $info = new Obj();
        
        $info->enable_link_couters  = TRUE;
        $info->city_id              = ($domain->get_city()) ? $domain->get_city()->id : NULL;
        $info->category_id          = $search_url->get_category()->id;
        $info->child_categories_ids = $search_url->get_category_childs_id();
        
        $info->s_host        = URL::SERVER("HTTP_HOST");
        $info->s_suri        = URL::SERVER("REQUEST_URI");
        $info->domain        = $domain;
        $info->city          = $domain->get_city();
        $info->main_category = $domain->get_main_category();
        
        $info->category_url    = $search_url->get_proper_category_uri();
        $info->seo_segment_url = $search_url->get_proper_seo_param_uri();
        $info->url             = $info->s_host . "/" . $info->category_url;
        $info->canonical_url   = $search_url->get_proper_segments();
        if ($info->canonical_url === $info->main_category) {
            $info->canonical_url = "";
        }
        
        
        $info->category                         = $search_url->get_category();
        $info->category_childs                  = $search_url->get_category_childs(TRUE);

        $info->category_childs_elements         = $search_url->get_category_childs_elements($info->category_id, $info->city_id, $search_url->get_seo_filters());
        $info->category_childs_elements_colsize = Kohana::$config->load("landing.subfilters." . $info->category_id);
        $info->crumbs                           = array_merge($search_url->get_category_crubms($info->category_id), $search_url->get_seo_elements_crubms($search_url->get_seo_filters(), $info->category_url));
        $info->incorrectly_query_params_for_seo = $search_url->incorrectly_query_params_for_seo;
        $info->search_filters                   = array(
            "active" => TRUE,
            "published" => TRUE,
            "city_id" => $info->city_id,
            "category_id" => (count($info->child_categories_ids) > 0) ? $info->child_categories_ids : $info->category_id,
            
            "user_id" => $search_url->get_reserved_query_params("user_id"),
            "source" => $search_url->get_reserved_query_params("source"),
            "photo" => $search_url->get_reserved_query_params("photo"),
            "video" => $search_url->get_reserved_query_params("video"),
            "period" => $search_url->get_reserved_query_params("period"),
            "private" => $search_url->get_reserved_query_params("private"),
            "org" => $search_url->get_reserved_query_params("org"),
            "filters" => $clean_query_params
        );
        //Для рубрики новостей включаем фильтр по дате старта показа
        if ($info->category->seo_name == 'novosti') {
            $info->search_filters["expiration"] = TRUE;
        }
        
        if ($info->category->seo_name == 'glavnaya-kategoriya') {
            $info->search_filters["not_category_seo_name"] = array(
                "novosti"
            );
        }
        
        $info->seo_attributes = Seo::get_seo_attributes($search_url->get_proper_segments(), $info->search_filters["filters"], $search_url->get_category(), $domain->get_city());
        
        $info->clean_query_params = $clean_query_params;
        
        $info->query_params_for_js = json_encode(array_merge($search_url->get_query_params_without_reserved($request->query()), $clean_query_params));

        return $info;
    }

    public function cache_stat($info, $search_params)
    {
        
        $result = array();
        
        $result['ids'] = array_map(function($item)
        {
            return $item["id"];
        }, $info->main_search_result);
        
        $result['title']   = isset($info->seo_attributes["h1"]) ? $info->seo_attributes["h1"] : $info->category->title;
        $result['url']     = "http://" . $info->s_host . $info->s_suri;
        $result['page']    = (isset($search_params["page"])) ? $search_params["page"] : 1;
        $result['city_id'] = $info->city_id;
        
        Cachestat::factory($info->category_id . "search")->add(sha1(serialize($result)), $result);
    }

    public function save_search_info_to_cache($query_params, $options = array())
    {
        $suri_without_reserved = Search_Url::get_suri_without_reserved($query_params);
        
        $options = new Obj($options);
        if ($options->canonical_url) {
            $options->canonical_url = "/" . $options->canonical_url;
        }
        $suc = ORM::factory('Search_Url_Cache')->save_search_info($options->info, URL::SERVER("HTTP_HOST") . URL::SERVER("PATH_INFO") . $suri_without_reserved, URL::SERVER("HTTP_HOST") . $options->canonical_url, $options->sql, $options->count);
        return $suc;
    }

    static public function get_search_info_from_cache(Request $request)
    {
        $suri_without_reserved = Search_Url::get_suri_without_reserved( $request->query() );
        $search_info           = ORM::factory('Search_Url_Cache')->get_search_info(URL::SERVER("HTTP_HOST") . URL::SERVER("PATH_INFO") . $suri_without_reserved)->find();
        
        return ($search_info->loaded()) ? $search_info->get_row_as_obj() : FALSE;
    }


}