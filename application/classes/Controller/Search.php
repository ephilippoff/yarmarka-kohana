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
        $get_params = $this->request->query();

        try {
            $searchuri = new Search_Url($uri, $get_params);
        } catch (Exception $e) {
            //TODO Log incorrect seo
            HTTP::redirect("/", 301);
        }

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

        $this->params_by_uri = $searchuri;
    }

    public function action_index() {

        $twig = Twig::factory('search/index');

        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        $twig->crumbs      = Search_Url::get_category_crubms($this->params_by_uri->get_category()->id);

        echo Debug::vars($this->params_by_uri);

        $this->response->body($twig);

    }
    
} // End Search
