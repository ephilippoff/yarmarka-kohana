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

        $this->city = $this->domain->get_city();
        $this->last_city_id =( $this->city ) ?  $this->city->id : NULL;

        // $this->theme_class = "default";
        // $this->theme_img = "themes/default.png";

        // $config = Kohana::$config->load("landing");
        // $config = $config["cities"];
        // $subdomain = $this->domain->get_subdomain();
        // if ( in_array( $subdomain,  array_keys((array) $config)) ) {
        //     $this->theme_class = $config[$subdomain]["theme_class"];
        //     $this->theme_img = $config[$subdomain]["theme_img"];
        // }
    }

    public function action_index() {

        $twig = Twig::factory('index/index');

        $twig->last_city_id = $this->last_city_id;
        $last_city = NULL;
        if ($twig->last_city_id) {
            $twig->last_city = $last_city = ORM::factory('City', $twig->last_city_id)->get_row_as_obj();
        }

        $twig->months = Date::get_months_names();

         $search_query = Search::searchquery(
            array(
                "expiration" => TRUE,
                "premium" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $this->last_city_id,
                "category_seo_name" => "novosti"
            ),
            array("limit" => 2, "page" => 1)
        );
        $twig->premiumnews = Search::getresult($search_query->execute()->as_array());
        
        $premium_ids = array_map(function($item){
            return $item["id"];
        }, $twig->premiumnews);

        $search_query = Search::searchquery(
            array(
                "expiration" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $this->last_city_id,
                "category_seo_name" => "novosti",
                "not_id" => $premium_ids
            ),
            array("limit" => 15, "page" => 1)
        );
        $twig->lastnews = Search::getresult($search_query->execute()->as_array());

       
        $index_info = $this->get_index_info($last_city);

        $index_info->link_counters = Search_Url::getcounters($index_info->s_host, "", $index_info->categories["main"]);

        foreach (array("nizhnevartovsk","tyumen","surgut","nefteyugansk", FALSE) as $city_seo) {
            $city_counter = Search_Url::getcounters($this->domain->get_domain_by_city($city_seo, "glavnaya-kategoriya", ""), "", array( new Obj(array("url"=>"")) ) );
            $index_info->link_counters = array_merge($index_info->link_counters, $city_counter);
        }

        foreach ((array) $index_info as $key => $item) {
            $twig->{$key} = $item;
        }

        $premium_kupons = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "expiration" => TRUE,
                "premium" => TRUE,
                "category_id" => array(173),
                "city_id" => ($this->last_city_id) ? array($this->last_city_id) : NULL,
            ),
            array("limit" => 2)
        );

        $twig->premium_kupons = Search::getresult($premium_kupons->execute()->as_array());

        $kupons = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "expiration" => TRUE,
                "category_id" => array(173),
                "city_id" => ($this->last_city_id) ? array($this->last_city_id) : NULL,
            ),
            array("limit" => 10)
        );

        $twig->kupons = Search::getresult($kupons->execute()->as_array());

        $attachments = ORM::factory('Object_Attachment')
                            ->order_by("id","desc")
                            ->limit(3)
                            ->getprepared_all();
        $promo_thumbnails = array_map(function($item){
            return Imageci::getSavePaths($item->filename);
        }, $attachments);
        $twig->promo_thumbnails = $promo_thumbnails;

        $this->response->body($twig);
    }

    public function get_index_info($last_city = NULL) {
        $info = new Obj();

        $info->domain      = $this->domain;
        $info->city        = $this->domain->get_city();
        $info->main_category = $this->domain->get_main_category();

        $info->s_host = $_SERVER["HTTP_HOST"];
        $info->s_suri = $_SERVER["REQUEST_URI"];

        $info->cities = array(
            'nizhnevartovsk'=>'Нижневартовск',
            'surgut'=>'Сургут',
            'tyumen'=>'Тюмень','
            nefteyugansk'=>'Нефтеюганск'
        );

        $info->categories = ORM::factory('Category')->get_categories_extend(array(
            "with_child" => TRUE, 
            "with_ads" => TRUE, 
            "city_id" => NULL
        ));

        $info->categories["main"]= array_map(function($item) use ($last_city){
            if ($last_city) {
                $item->url = Domain::get_domain_by_city($last_city->seo_name, $item->seo_name);
            } else {
                $item->url = $item->seo_name;
            }
            return $item;
        }, $info->categories["main"] );

        $info->categories["childs"]= array_map(function($item) use ($last_city){
            if ($last_city) {
                $item->url = Domain::get_domain_by_city($last_city->seo_name, $item->seo_name);
            } else {
                $item->url = $item->seo_name;
            }
            return $item;
        }, $info->categories["childs"] );
        
        // $info->theme = new Obj(array(
        //     "theme_class" => $this->theme_class,
        //     "theme_img" => $this->theme_img
        // ));

        return $info;
    }
} // End Index
