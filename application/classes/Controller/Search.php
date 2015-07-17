<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Controller_Template {

    

    public function before()
    {
        parent::before();

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        $this->cached_search_info = FALSE;

        if ($search_info = $this->get_search_info_from_cache() AND 1==0) {
            $this->cached_search_info = unserialize($search_info->params);
        } else {

            $this->domain = new Domain();
            if ($proper_domain = $this->domain->is_domain_incorrect()) {
                HTTP::redirect("http://".$proper_domain, 301);
            }

            $uri = $this->request->uri();
            $route_params = $this->request->param();
            $query_params = $this->request->query();

            try {
                $searchuri = new Search_Url($uri, $query_params, $route_params);
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
        $start = microtime(true);

        $twig = Twig::factory('search/index');

        $search_info = $this->get_search_info();

        //main search
        $main_search_query = Search::searchquery($search_info->search_filters, $search_info->search_params);
        
        $twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());


        if (!$search_info->main_search_result_count) {
            $main_search_result_count = Search::searchquery($search_info->search_filters, array(), array("count" => TRUE))
                                                    ->execute()
                                                    ->get("count");
            $search_info->main_search_result_count = $main_search_result_count;
        }
        //end main search

        //premium
        $premium_search_query = Search::searchquery(
            array_merge($search_info->search_filters, array("premium" => TRUE)), 
            array_merge($search_info->search_params, array("limit" => 5))
        );
        $twig->premium_search_result = Search::getresult($premium_search_query->execute()->as_array());
        //premium end
        
        //vip
        $vip_search_query = Search::searchquery(
            array(
                "photocard" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $search_info->city_id,
                "category_id" => (count($search_info->child_categories_ids) > 0) ? $search_info->child_categories_ids : $search_info->category->id,
            ),
            array_merge($search_info->search_params, array("limit" => 15))
        );
        $twig->vip_search_result = Search::getresult($vip_search_query->execute()->as_array());
        //vip end

        //pagination
        $pagination = Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_info->search_params['limit'],
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'count_out' => 1,
            'count_in' => 8,
            'limits' => array(
                "30" => $this->url_with_query(array(), array("page","limit")),
                "60" => $this->url_with_query(array( "limit" => 60), array("page")),
                "90" => $this->url_with_query(array( "limit" => 90), array("page")),
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

        if (!$search_info->incorrectly_query_params_for_seo AND !$this->cached_search_info) {
            $this->save_search_info_to_cache(array(
                    "info" => $search_info,
                    "canonical_url" =>  $search_info->canonical_url,
                    "sql" => (string) $main_search_query,
                    "count" => $search_info->main_search_result_count,
                )
            );
        }

        foreach ((array) $search_info as $key => $item) {
            $twig->{$key} = $item;
        }

        $twig->php_time = microtime(true) - $start;
        $this->response->body($twig);
    }

    public function get_search_info() {
        if ($this->cached_search_info) {
            return new Obj($this->cached_search_info);
        }

        $info = new Obj();

        $info->city_id = ($this->domain->get_city()) ? $this->domain->get_city()->id : NULL;
        $info->category_id = $this->params_by_uri->get_category()->id;
        $info->child_categories_ids = $this->params_by_uri->get_category_childs_id();

        $info->host = $_SERVER["HTTP_HOST"];
        $info->category_url = $this->params_by_uri->get_proper_category_uri();
        $info->url = $info->host."/".$info->category_url;
        $info->canonical_url  =  $this->params_by_uri->get_proper_segments();
        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();
        $info->category = $this->params_by_uri->get_category();
        $info->category_childs = $this->params_by_uri->get_category_childs(TRUE);
        $info->category_childs_elements = $this->params_by_uri->get_category_childs_elements($info->category_id, $this->params_by_uri->get_seo_filters());
        $info->link_counters = $this->params_by_uri->getcounters($info->host, $info->category_url, array_merge($info->category_childs, $info->category_childs_elements) );
        $info->category_childs_elements = $this->params_by_uri->clean_empty_category_childs_elements($info->category_childs_elements, $info->link_counters, $info->url);
        $info->crumbs      = $this->params_by_uri->get_category_crubms($info->category_id);
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

            "filters" => array_merge($this->params_by_uri->get_clean_query_params(), $this->params_by_uri->get_seo_filters())
        );

        $info->search_params = array(
            "page" => $this->params_by_uri->get_reserved_query_params("page"),
            "limit" => $this->params_by_uri->get_reserved_query_params("limit"),
        );

        $info->seo_attributes = Seo::get_seo_attributes(
            $this->params_by_uri->get_proper_segments(),
            $info->search_filters["filters"],
            $this->params_by_uri->get_category(),
            $this->domain->get_city()
        );

        return $info;
    }

    public function save_search_info_to_cache($options = array()) {
        $options = new Obj($options);
        ORM::factory('Search_Url_Cache')
                ->save_search_info(
                    $options->info, 
                    $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"], 
                    $_SERVER["HTTP_HOST"]."/".$options->canonical_url, 
                    $options->sql, 
                    $options->count
                );
    }

    public function get_search_info_from_cache() {
        $search_info = ORM::factory('Search_Url_Cache')
                        ->get_search_info($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])
                        ->find();

        return ($search_info->loaded()) ? $search_info->get_row_as_obj() : FALSE;
    }

    public function url_with_query($params = array(), $unset_params = array()) {
        $query_params = $this->request->query();
        foreach ($params as $key => $value) {
            $query_params[$key] = $value;
        }
        foreach ($unset_params as $unset_param) {
            unset($query_params[$unset_param]);
        }

        $query_str = http_build_query($query_params);
        return $this->request->route()->uri($this->request->param()).($query_str?"?".$query_str:"");
    }
    
} // End Search
