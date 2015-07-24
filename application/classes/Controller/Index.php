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

        $this->theme_class = "default";
        $this->theme_img = "themes/default.png";

        $config = Kohana::$config->load("landing");
        $config = $config["cities"];
        $subdomain = $this->domain->get_subdomain();
        if ( in_array( $subdomain,  array_keys((array) $config)) ) {
            $this->theme_class = $config[$subdomain]["theme_class"];
            $this->theme_img = $config[$subdomain]["theme_img"];
        }
    }

    public function action_index() {
        $start = microtime(true);
        
        $twig = Twig::factory('index/index');

        // $twig->lastnews  = ORM::factory('Article')
        //                         ->get_lastnews(NULL, NULL, 5)
        //                         ->getprepared_all();
        
        $index_info = $this->get_index_info();

        foreach ((array) $index_info as $key => $item) {
            $twig->{$key} = $item;
        }
        $twig->php_time = microtime(true) - $start;
        $this->response->body($twig);
    }

    public function action_city() {
        $start = microtime(true);
        $twig = Twig::factory('index/index_city');


        // $twig->categories = ORM::factory('Category')->get_categories_extend(array(
        //     "with_child" => TRUE, 
        //     "with_ads" => TRUE, 
        //     "city_id" => $twig->city->id
        // ));

        // $twig->theme = new Obj(array(
        //     "theme_class" => $this->theme_class,
        //     "theme_img" => $this->theme_img
        // ));

        // $twig->lastnews  = ORM::factory('Article')
        //                         ->get_lastnews($twig->city->id , NULL, 5)
        //                         ->getprepared_all();

        // $twig->companies  = ORM::factory('User')
        //                         ->get_good_companies($twig->city->id)
        //                         ->getprepared_all();
        
        $index_info = $this->get_index_info();
        foreach ((array) $index_info as $key => $item) {
            $twig->{$key} = $item;
        }
        $twig->php_time = microtime(true) - $start;
        $this->response->body($twig);
    }

    public function get_index_info() {
        $info = new Obj();

        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();

        $info->s_host = $_SERVER["HTTP_HOST"];
        $info->s_suri = $_SERVER["REQUEST_URI"];

        

        $info->categories = ORM::factory('Category')->get_categories_extend(array(
            "with_child" => TRUE, 
            "with_ads" => TRUE, 
            "city_id" => NULL
        ));



        $info->categories["main"]= array_map(function($item){
            $item->url = $item->seo_name;
            return $item;
        }, $info->categories["main"] );

        
        
        $info->link_counters = Search_Url::getcounters($info->s_host, "", $info->categories["main"]);
        foreach (array("nizhnevartovsk","tyumen","surgut","nefteyugansk", FALSE) as $city_seo) {
            $city_counter = Search_Url::getcounters($this->domain->get_domain_by_city($city_seo, FALSE, ""), "", array( new Obj(array("url"=>"glavnaya-kategoriya")) ) );
            $info->link_counters = array_merge($info->link_counters, $city_counter);
        }
        
        $info->theme = new Obj(array(
            "theme_class" => $this->theme_class,
            "theme_img" => $this->theme_img
        ));

        return $info;
    }
} // End Index
