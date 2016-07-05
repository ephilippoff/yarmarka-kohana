<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Search extends Controller_Template
{

    public function before()
    {
        
        parent::before();
        
        $this->use_layout         = FALSE;
        $this->auto_render        = FALSE;
        $this->cached_search_info = FALSE;
        $this->cached = FALSE;
        
        if ($search_info = Searchpage_Default::get_search_info_from_cache($this->request) ) {

            $this->cached_search_info = unserialize($search_info->params);
            $this->cached = TRUE;
            Cookie::set('search_hash', $search_info->hash, strtotime('+14 days'));
            
        } else {
            
            $this->domain = new Domain();
            if ($proper_domain = $this->domain->is_domain_incorrect()) {
                HTTP::redirect("http://" . $proper_domain, 301);
            }
            
            $uri          = $this->request->uri();
            $route_params = $this->request->param();
            $query_params = $this->request->query();
            
            if (@$query_params['k']) {
                
                $query_params = array(
                    'search' => $query_params['k']
                );
                HTTP::redirect($uri . "=" . http_build_query($query_params), 301);
                return;
            }
            
            try {
                $searchuri = new Search_Url($route_params['category_path'], $query_params, ($this->domain->get_city()) ? $this->domain->get_city()->id : FALSE);
            }
            catch (Kohana_Exception $e) {
                //TODO Log incorrect seo
                //HTTP::redirect("/", 301);
            }
            
            try {
                $searchuri->check_uri_segments();
            }
            catch (Kohana_Exception_Withparams $e) {
                $error_params = $e->getParams();
                HTTP::redirect($error_params["uri"], $error_params["code"]);
            }
            
            try {
                $searchuri->check_query_params($query_params);
            }
            catch (Kohana_Exception_Withparams $e) {
                $searchuri->incorrectly_query_params_for_seo = TRUE;
            }
            
            $this->params_by_uri = $searchuri;
            
        }
    }

    public function action_adverts()
    {

        $search = new Searchpage_Adverts();

        if ($this->cached) {
            $search_info =  new Obj($this->cached_search_info);
        } else {
            $search_text = $this->params_by_uri->get_reserved_query_params("search");
            $search_info = ($search_text) ? 
                                 $search->get_search_info_by_sphinx($search_text, $this->params_by_uri, $this->domain, $this->request):
                                $search->get_search_info($this->params_by_uri, $this->domain, $this->request);
        }

        $this->response->body($search->get_twig_data($search_info, $this->request, $this->cached));

    }

    public function action_kupony()
    {
        $search = new Searchpage_Kupony();

        $search_info = ($this->cached) ? 
                            new Obj($this->cached_search_info): 
                                    $search->get_search_info($this->params_by_uri, $this->domain, $this->request);
        
        $this->response->body($search->get_twig_data($search_info, $this->request, $this->cached));

    }

    public function action_novosti()
    {

        $search = new Searchpage_News();

        $search_info = ($this->cached) ? 
                            new Obj($this->cached_search_info): 
                                    $search->get_search_info($this->params_by_uri, $this->domain, $this->request);
        
        $this->response->body($search->get_twig_data($search_info, $this->request, $this->cached));

    }

    public function after()
    {
        parent::after();
    }
} // End Search
