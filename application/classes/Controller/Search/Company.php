<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search_Company extends Controller_Template {

    public function before()
    {
        parent::before();
        
        $this->use_layout = FALSE;
        $this->auto_render = FALSE;

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
        $this->city = $this->domain->get_city();
    }

    public function action_index(){
         $twig = Twig::factory('other/companies');
         $city = $this->city;
         $this->response->body($twig);
    }
}
