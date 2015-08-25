<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Controller_Template {

    

    public function before()
    {
        $this->performance = Performance::factory(Acl::check('profiler'));
        $this->performance->add("Search","start");
        parent::before();

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        $this->cached_search_info = FALSE;

        if ($search_info = $this->get_search_info_from_cache()) {
            $this->cached_search_info = unserialize($search_info->params);
            Cookie::set('search_hash', $search_info->hash, strtotime( '+14 days' ));
        } else {

            $this->domain = new Domain();
            if ($proper_domain = $this->domain->is_domain_incorrect()) {
                HTTP::redirect("http://".$proper_domain, 301);
            }

            $uri = $this->request->uri();
            $route_params = $this->request->param();
            $query_params = $this->request->query();

            try {
                $searchuri = new Search_Url($route_params['category_path'], $query_params, ($this->domain->get_city()) ? $this->domain->get_city()->id : FALSE);
            } catch (Kohana_Exception $e) {
                //TODO Log incorrect seo
                //HTTP::redirect("/", 301);
            }

            try {
                $searchuri->check_uri_segments();
            } catch (Kohana_Exception_Withparams $e) {
                $error_params = $e->getParams();
                HTTP::redirect($error_params["uri"], $error_params["code"]);
            }

            try {
                $searchuri->check_query_params($query_params);
            } catch (Kohana_Exception_Withparams $e) {
                $searchuri->incorrectly_query_params_for_seo = TRUE;
            }

            $this->params_by_uri = $searchuri;

        }
    }

    public function action_index() {

        $this->performance->add("Search","staticfile");
        $objects_for_map = array();
        $prefix = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
        $staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');

        $this->performance->add("Search","search_info");
        $search_info = $this->get_search_info();

        $search_params = Search_Url::clean_reserved_query_params($this->request->query());

        //link counters
        $this->performance->add("Search","link_couters");
        if ($search_info->enable_link_couters) {
            $search_info->link_counters = Search_Url::getcounters($search_info->s_host, $search_info->category_url, array_merge($search_info->category_childs, $search_info->category_childs_elements) );
            foreach (array("nizhnevartovsk","tyumen","surgut","nefteyugansk", FALSE) as $city_seo) {
                $city_counter = Search_Url::getcounters(Domain::get_domain_by_city($city_seo, FALSE, ""), "", array( new Obj(array("url"=>$search_info->canonical_url)) ) );
                $search_info->link_counters = array_merge($search_info->link_counters, $city_counter);
            }
            $search_info->link_counters[$search_info->s_host.$search_info->s_suri] = $search_info->main_search_result_count;
            
            //clean empty links
            $search_info->category_childs_elements = Search_url::clean_empty_category_childs_elements($search_info->category_childs_elements, $search_info->link_counters, $search_info->url);
            // end clean empty links
        }
        //link counters end
        
        $twig = Twig::factory('search/index');
        $twig->data_file = $staticfile->jspath;

        //main search
        $this->performance->add("Search","main_search_query");
        $main_search_query = Search::searchquery($search_info->search_filters, $search_params);

        $twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());
        if (!$search_info->main_search_result_count) {
                $main_search_result_count = Search::searchquery($search_info->search_filters, array(), array("count" => TRUE))
                                                        ->execute()
                                                        ->get("count");
                $search_info->main_search_result_count = $main_search_result_count;
        }

        if (count($twig->main_search_result) > 0) 
        {
            $main_search_coords = array_map(function($item){
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "price" => $item["price"],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(@$item["compiled"]["lat"], @$item["compiled"]["lon"])
                );
            }, $twig->main_search_result);

            $objects_for_map = array_merge($objects_for_map, $main_search_coords);
        }
        //end main search

        //premium
        $this->performance->add("Search","premium_search_query");
        $premium_search_query = Search::searchquery(
            array_merge($search_info->search_filters, array("premium" => TRUE)), 
            array_merge($search_params, array("limit" => 5))
        );
        $twig->premium_search_result = Search::getresult($premium_search_query->execute()->as_array());
        foreach ($twig->premium_search_result as $key => $value) {
            $twig->premium_search_result[$key]["is_premium"] = TRUE;
        }
        if (count($twig->premium_search_result) > 0) 
        {
            $premium_search_coords = array_map(function($item){
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "type" => "premium",
                    "price" => $item["price"],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(@$item["compiled"]["lat"], @$item["compiled"]["lon"])
                );
            }, $twig->premium_search_result);

            $objects_for_map = array_merge($objects_for_map, $premium_search_coords);
        }
        //premium end
       
        //vip
        $this->performance->add("Search","vip_search_query");
        $vip_search_query = Search::searchquery(
            array(
                "search_text" => @$search_info->search_filters["search_text"],
                "photocard" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $search_info->city_id,
                "category_id" => (count($search_info->child_categories_ids) > 0) ? $search_info->child_categories_ids : $search_info->category->id,
            ),
            array_merge($search_params, array("limit" => 15))
        );
        $twig->vip_search_result = Search::getresult($vip_search_query->execute()->as_array());
        if (count($twig->vip_search_result) > 0) 
        {
            $vip_search_coords = array_map(function($item){
                return array(
                    "id" => $item["id"],
                    "title" => addslashes($item["title"]),
                    "type" => "lider",
                    "price" => $item["price"],
                    "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                    "coords" => array(@$item["compiled"]["lat"], @$item["compiled"]["lon"])
                );
            }, $twig->vip_search_result);

            $objects_for_map = array_merge($objects_for_map, $vip_search_coords);
        }
        //vip end

        //pagination
        $pagination = Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_params['limit'],
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'count_out' => 1,
            'count_in' => 8,
            'path' => URL::SERVER("PATH_INFO"),
            'limits' => array(
                "30" => Search_Url::get_suri_without_reserved($this->request->query(),array(),array("limit","page")),
                "60" => Search_Url::get_suri_without_reserved($this->request->query(), array( "limit" => 60), array("page")),
                "90" => Search_Url::get_suri_without_reserved($this->request->query(), array( "limit" => 90), array("page")),
            )
        ));
        $twig->small_pagination = (array(
            "prev" => $pagination->previous_page,
            "prev_url" => $pagination->url($pagination->previous_page),
            "next" => $pagination->next_page,
            "next_url" => $pagination->url($pagination->next_page),
            "current" => $pagination->current_page,
            "total" => $pagination->total_pages,
        ));
        $twig->pagination = $pagination;
        //pagination end

        
        //save search settings cache
        $this->performance->add("Search","save_search_settings_cache");
        if (!$this->cached_search_info AND !$search_info->search_text) {
            $cache = $this->save_search_info_to_cache(array(
                    "info" => $search_info,
                    "canonical_url" =>  $search_info->canonical_url,
                    "sql" => (string) $main_search_query,
                    "count" => $search_info->main_search_result_count,
                )
            );
            Cookie::set('search_hash', $cache->hash, strtotime( '+14 days' ));
            
        }
        //save search settings cache end


        

        
        $this->performance->add("Search","additional");
        //favourites
        $twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
        //end favourites
        
        if ($search_info->category->show_map or count($twig->vip_search_result) > 6) {
            if (count($objects_for_map) > 0 ) {
                $twig->objects_for_map = json_encode($objects_for_map);
                $twig->set_filename('search/index/with_map');
            }
        }
		
		if ($search_info->category->seo_name == 'kupony')
			$twig->set_filename('search/kupony/index');		
		
        $this->performance->add("Search","end");
        foreach ((array) $search_info as $key => $item) {
            $twig->{$key} = $item;
        }        
        $this->cache_stat($twig, $search_params);
        $twig->php_time = $this->performance->getProfilerStat();
        $this->response->body($twig);

    }

    public function cache_stat($info, $search_params)
    {

        $result = array();

        $result['ids'] = array_map(function($item){
            return $item["id"];
        }, $info->main_search_result);

        $result['title'] = isset($info->seo_attributes["h1"]) ? $info->seo_attributes["h1"] : $info->category->title;
        $result['url'] = "http://".$info->s_host.$info->s_suri;
        $result['page'] = ( isset($search_params["page"]) ) ? $search_params["page"] : 1;
        $result['city_id'] = $info->city_id;

        Cachestat::factory($info->category_id."search")->add(sha1(serialize($result)), $result);
    }

    public function get_search_info()
    {
        if ($this->cached_search_info) {
            return new Obj($this->cached_search_info);
        }

        $search_text = $this->params_by_uri->get_reserved_query_params("search");
        if ($search_text) {
            return $this->get_search_info_by_sphinx($search_text);
        }

        return $this->get_search_info_by_filters();
    }

    public function get_search_info_by_filters()
    {
        $clean_query_params = array_merge($this->params_by_uri->get_clean_query_params(), $this->params_by_uri->get_seo_filters());

        $info = new Obj();

        $info->enable_link_couters = TRUE;
        $info->city_id = ($this->domain->get_city()) ? $this->domain->get_city()->id : NULL;
        $info->category_id = $this->params_by_uri->get_category()->id;
        $info->child_categories_ids = $this->params_by_uri->get_category_childs_id();

        $info->s_host = URL::SERVER("HTTP_HOST");
        $info->s_suri = URL::SERVER("REQUEST_URI");
        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();
        $info->main_category = $this->domain->get_main_category();

        $info->category_url = $this->params_by_uri->get_proper_category_uri();
        $info->seo_segment_url = $this->params_by_uri->get_proper_seo_param_uri();
        $info->url = $info->s_host."/".$info->category_url;
        $info->canonical_url  =  $this->params_by_uri->get_proper_segments();
        if ($info->canonical_url === $info->main_category) {
            $info->canonical_url = "";
        }
        if ($info->s_suri <> "/".$info->canonical_url) {
            $info->show_canonical = TRUE;
            $info->is_canonical = FALSE;
        } else {
             $info->is_canonical = TRUE;
        }

        $info->category = $this->params_by_uri->get_category();
        $info->category_childs = $this->params_by_uri->get_category_childs(TRUE);
        $info->category_childs_elements = $this->params_by_uri->get_category_childs_elements($info->category_id, $info->city_id, $this->params_by_uri->get_seo_filters());
        $info->category_childs_elements_colsize = Kohana::$config->load("landing.subfilters.".$info->category_id);
        $info->crumbs      = array_merge($this->params_by_uri->get_category_crubms($info->category_id), $this->params_by_uri->get_seo_elements_crubms($this->params_by_uri->get_seo_filters(), $info->category_url));
        $info->incorrectly_query_params_for_seo =  $this->params_by_uri->incorrectly_query_params_for_seo;
        $info->search_filters = array(
            "active" => TRUE,
            "published" =>TRUE,
            "city_id" => $info->city_id,
            "category_id" => (count($info->child_categories_ids) > 0) ? $info->child_categories_ids : $info->category_id,

            "user_id" => $this->params_by_uri->get_reserved_query_params("user_id"),
            "source" => $this->params_by_uri->get_reserved_query_params("source"),
            "photo" => $this->params_by_uri->get_reserved_query_params("photo"),
            "video" => $this->params_by_uri->get_reserved_query_params("video"),
            "private" => $this->params_by_uri->get_reserved_query_params("private"),
            "org" => $this->params_by_uri->get_reserved_query_params("org"),
            "filters" => $clean_query_params
        );

        $info->seo_attributes = Seo::get_seo_attributes(
            $this->params_by_uri->get_proper_segments(),
            $info->search_filters["filters"],
            $this->params_by_uri->get_category(),
            $this->domain->get_city()
        );
        
        $info->clean_query_params = $clean_query_params;

        $info->query_params_for_js = json_encode(
            array_merge(
                $this->params_by_uri->get_query_params_without_reserved($this->request->query()),
                $clean_query_params
            )
        );
        return $info;
    }

    public function get_search_info_by_sphinx($search_text)
    {
        $info = new Obj();

        $info->search_text = $search_text;

        $info->city_id = ($this->domain->get_city()) ? $this->domain->get_city()->id : NULL;
        $info->category_id = $this->params_by_uri->get_category()->id;
        $info->child_categories_ids = $this->params_by_uri->get_category_childs_id();

        $info->s_host = URL::SERVER("HTTP_HOST");
        $info->s_suri = trim(URL::SERVER("REQUEST_URI"),"/");
        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();
        $info->main_category = $this->domain->get_main_category();

        $sphinx = new Sphinx();
        $sphinx_category_childs = $sphinx->searchGroupByCategory( 
            $info->search_text, 
            $info->city_id,
            (count($info->child_categories_ids) > 0) ? $info->child_categories_ids : $info->category_id
        );
        $info->sphinx_category_childs = $sphinx_category_childs["categories"];
        $info->category_childs_elements_colsize = 4;

        $info->category_url = $this->params_by_uri->get_proper_category_uri();
        $info->url = $info->s_host."/".$info->category_url;
        $info->canonical_url  =  $this->params_by_uri->get_proper_segments();
        $info->sphinx_search_query = "?search=".$info->search_text;
        $info->dirty_url = $info->url.$info->sphinx_search_query;
        if ($info->canonical_url === $info->main_category) {
            $info->canonical_url = "";
        }
        if ($info->s_suri <> "/".$info->canonical_url.$info->sphinx_search_query) {
             $info->show_canonical = TRUE;
        }        
        $info->category = $this->params_by_uri->get_category();
        $info->crumbs      = $this->params_by_uri->get_category_crubms($info->category_id,  $info->sphinx_search_query);
        $info->incorrectly_query_params_for_seo =  $this->params_by_uri->incorrectly_query_params_for_seo;
        $info->search_filters = array(
            "active" => TRUE,
            "published" =>TRUE,
            "city_id" => $info->city_id,
            "category_id" => (count($info->child_categories_ids) > 0) ? $info->child_categories_ids : $info->category_id,

            //TODO фильтр по фото
            //"user_id" => $this->params_by_uri->get_reserved_query_params("user_id"),
            //"photo" => $this->params_by_uri->get_reserved_query_params("photo"),

            "search_text" => $info->search_text,
            "filters" => array()
        );

        $info->seo_attributes = Seo::get_seo_attributes(
            $this->params_by_uri->get_proper_segments(),
            $info->search_filters["filters"],
            $this->params_by_uri->get_category(),
            $this->domain->get_city()
        );

        
        return $info;
    }

    public function save_search_info_to_cache($options = array())
    {
        $suri_without_reserved = Search_Url::get_suri_without_reserved($this->request->query());

        $options = new Obj($options);
        if ($options->canonical_url) {
            $options->canonical_url = "/".$options->canonical_url;
        }
        $suc = ORM::factory('Search_Url_Cache')
                    ->save_search_info(
                        $options->info, 
                        URL::SERVER("HTTP_HOST").URL::SERVER("PATH_INFO").$suri_without_reserved, 
                        URL::SERVER("HTTP_HOST").$options->canonical_url, 
                        $options->sql, 
                        $options->count
                    );
        return $suc;
    }

    public function get_search_info_from_cache()
    {
        $suri_without_reserved = Search_Url::get_suri_without_reserved($this->request->query());
        $search_info = ORM::factory('Search_Url_Cache')
                        ->get_search_info(URL::SERVER("HTTP_HOST").URL::SERVER("PATH_INFO").$suri_without_reserved)
                        ->find();

        return ($search_info->loaded()) ? $search_info->get_row_as_obj() : FALSE;
    }
    
    public function after()
    {
        parent::after();
    }
} // End Search
