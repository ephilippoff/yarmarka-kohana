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
        $twig = Twig::factory('index/index');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();

        $twig->categories = ORM::factory('Category')->get_categories_extend(array(
            "with_child" => TRUE, 
            "with_ads" => TRUE, 
            "city_id" => NULL
        ));

        $twig->theme = new Obj(array(
            "theme_class" => $this->theme_class,
            "theme_img" => $this->theme_img
        ));

        $twig->lastnews  = ORM::factory('Article')
                                ->get_lastnews(NULL, NULL, 5)
                                ->getprepared_all();
        
        $this->response->body($twig);
    }

    public function action_city() {

        $twig = Twig::factory('index/city');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();

        $twig->categories = ORM::factory('Category')->get_categories_extend(array(
            "with_child" => TRUE, 
            "with_ads" => TRUE, 
            "city_id" => $twig->city->id
        ));

        $twig->theme = new Obj(array(
            "theme_class" => $this->theme_class,
            "theme_img" => $this->theme_img
        ));

        $twig->lastnews  = ORM::factory('Article')
                                ->get_lastnews($twig->city->id , NULL, 5)
                                ->getprepared_all();

        $twig->companies  = ORM::factory('User')
                                ->get_good_companies($twig->city->id)
                                ->getprepared_all();
        
        $this->response->body($twig);
    }
} // End Index
