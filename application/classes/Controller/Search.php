<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Controller_Template {

    

    public function before()
    {
        parent::before();

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }

        $uri = $this->request->uri();
        $query_params = $this->request->query();

       // try {
            $searchuri = new Search_Url($uri, $query_params);
        //} catch (Exception $e) {
            //TODO Log incorrect seo
            //HTTP::redirect("/", 301);
        //}

        if ($proper_category_uri = $searchuri->is_seo_category_segment_incorrect()) {
            //TODO Log incorrect category seo
            HTTP::redirect($proper_category_uri, 301);
        }

        if ($proper_seo_param_uri = $searchuri->is_seo_param_segment_incorrect()) {
            //TODO Log incorrect seo params
            if ($proper_seo_param_uri === TRUE) {
                HTTP::redirect($searchuri->get_proper_category_uri(), 301);
            } else {
                HTTP::redirect($searchuri->get_proper_category_uri()."/".$proper_seo_param_uri, 301);
            }
        }

        if (count($searchuri->get_reserved()) > 0 ) {
            $reserved = new Obj($searchuri->get_reserved());
            //TODO Log incorrect seo params
            if ($reserved->page AND $searchuri->is_page_incorrect($reserved->page)) {
                HTTP::redirect($searchuri->get_proper_category_uri(), 301);
            }
            //TODO Log incorrect seo params
            if ($reserved->limit AND $searchuri->is_limit_incorrect($reserved->limit)) {
                HTTP::redirect($searchuri->get_proper_category_uri(), 301);
            }
            //TODO Log incorrect seo params
            if ($reserved->order AND $searchuri->is_order_incorrect($reserved->order)) {
                HTTP::redirect($searchuri->get_proper_category_uri(), 301);
            }
        }

        if ($old_param = $searchuri->get_old_seo_query_param()) {
            HTTP::redirect($searchuri->get_proper_category_uri()."/".$searchuri->get_seo_param_segment($old_param), 301);
        }


        //check proper query params. if not proper set NOINDEX TODO
        $proper_query_params_uri_segment = $searchuri->get_query_params_segment( $searchuri->get_query_params() );
        if ($searchuri->get_uri_query_segment() <> "" 
                AND $searchuri->get_uri_query_segment() <>  $proper_query_params_uri_segment) {

            $searchuri->incorrectly_query_params = TRUE;
            $searchuri->correctly_query_params = $proper_query_params_uri_segment;
            
        }
        
        $this->params_by_uri = $searchuri;
    }

    public function action_index() {

        $twig = Twig::factory('search/index');

        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        $twig->crumbs      = Search_Url::get_category_crubms($this->params_by_uri->get_category()->id);

        $search_query = Search::searchquery(array(
            "active" => TRUE,
            "published" =>TRUE,
            "city_id" => array(1919),
            "category_id" => 96,
            "page" => 1,
            "limit" => 3
        ));

        echo Debug::vars($this->params_by_uri);

        echo Debug::vars($search_query->execute());

        $this->response->body($twig);

    }
    
} // End Search
