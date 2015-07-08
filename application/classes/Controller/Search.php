<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Controller_Template {

    

    public function before()
    {
        parent::before();

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
    }

    public function action_index() {

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

        echo Debug::vars($this->domain, $searchuri);

    }
    
} // End Search
