<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Template {

    public function before()
    {
        parent::before();

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
    }

    public function action_index() {

        $twig = Twig::factory('index/index');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        
        $this->response->body($twig);
    }

    public function action_city() {

        $twig = Twig::factory('index/city');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        
        $this->response->body($twig);
    }
} // End Index
