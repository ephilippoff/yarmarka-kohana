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

        $twig = Twig::factory('search/index');

        $search_info = $this->get_search_info();

        //main search
        $main_search_query = Search::searchquery($search_info->search_filters, $search_info->search_params);
        $twig->main_search_result = $main_search_query->execute()->as_array();

        if (!$search_info->main_search_result_count) {
            $main_search_result_count = Search::searchquery($search_info->search_filters, array(), array("count" => TRUE))
                                                    ->execute()
                                                    ->get("count");
            $search_info->main_search_result_count = $main_search_result_count;
        }

        //premium
        $premium_search_query = Search::searchquery(
            array_merge($search_info->search_filters, array("premium" => TRUE)), 
            array_merge($search_info->search_params, array("limit" => 5))
        );
        $twig->premium_search_result = $premium_search_query->execute()->as_array();


        //pagination
        $twig->pagination =  Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_info->search_params['limit'],
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'count_out' => 1,
            'count_in' => 8
        ));

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
        $this->response->body($twig);

    }

    public function get_search_info() {
        if ($this->cached_search_info) {
            return new Obj($this->cached_search_info);
        }

        $info = new Obj();

        $city_id = ($this->domain->get_city()) ? $this->domain->get_city()->id : NULL;
        $category_id = $this->params_by_uri->get_category()->id;
        $child_categories_ids = $this->params_by_uri->get_category_childs_id();

        $info->canonical_url  =  $this->params_by_uri->get_proper_segments();
        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();
        $info->crumbs      = Search_Url::get_category_crubms($category_id);
        $info->incorrectly_query_params_for_seo =  $this->params_by_uri->incorrectly_query_params_for_seo;
        $info->search_filters = array(
            "active" => TRUE,
            "published" =>TRUE,
            "city_id" => $city_id,
            "category_id" => (count($child_categories_ids) > 0) ? $child_categories_ids : $category_id,

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
    
} // End Search
