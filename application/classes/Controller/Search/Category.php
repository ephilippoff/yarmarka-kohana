<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search_Category extends Controller_Template {

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
         $twig = Twig::factory('other/categories');

         $twig->categories = ORM::factory('Category')->get_categories_extend(array(
             "with_child" => TRUE, 
             "with_ads" => TRUE, 
             "city_id" => NULL
         ));

        $city = $this->city;
        $twig->s_host = $_SERVER["HTTP_HOST"];

        $counters_parents = Search_Url::getcounters($twig->s_host, "", $twig->categories["main"]);
        $counters_childs = Search_Url::getcounters($twig->s_host, "", $twig->categories["childs"]);

        $twig->link_counters = array_merge($counters_parents, $counters_childs);
        $this->response->body($twig);
    }
}
