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

    public function action_map() {

        $city_id = $this->request->post("city_id");
        $category_id = $this->request->post("category_id");
        $search_filters = $this->request->post("search_filters");

        


        if (!$search_filters) {
            $search_filters = array(
                "active" => TRUE,
                "published" => TRUE,
                "city_id" => $city_id,
                "category_id" => $category_id,
                "source" => 1
            );
        } else {
            $search_filters = json_decode(json_encode(json_decode($search_filters)), True);
        }
     
        $main_search_query = Search::searchquery($search_filters, array( 
            "limit" => 1000,
            "page" => 1
        ));
        
        $main_search_result = Search::getresult($main_search_query->execute()->as_array());

        $main_search_coords =array(
            "type" => "FeatureCollection",
            "features" => array()
        );

        if (count($main_search_result) > 0) {
            $main_search_coords['features'] = array_map(function($item)
            {   

                $baloon = sprintf('<div class="clearfix m-map__baloon"><img src="/%s" class="m-map__baloon_left"><div class="m-map__baloon_right "><p><a href="/detail/%d">%s</a></p><p>%s</p></div></div>', 
                    (@$item["compiled"]["images"]["main_photo"]["120x90"]) ? @$item["compiled"]["images"]["main_photo"]["120x90"] : 'static/develop/images/nophoto136x107.png',
                     $item["id"],
                     addslashes($item["title"]),
                    ($item['price']) ? $item['price']." руб" : ""
                );

                return array(
                    "type" => "Feature",
                    "id" => $item["id"],
                    "geometry" => array(
                      "type" => "Point",
                      "coordinates" => array(@$item["compiled"]["lat"], @$item["compiled"]["lon"])
                    ),
                    "properties" => array(
                      "balloonContent"=> $baloon,
                      "clusterCaption"=> addslashes($item["title"]),
                      "hintContent" => addslashes($item["title"]),
                    )
                );


                // "id" => $item["id"],
                // "title" => addslashes($item["title"]),
                // "price" => $item['price'],
                // "photo" => @$item["compiled"]["images"]["main_photo"]["120x90"],
                // "coords" => array(
                //     @$item["compiled"]["lat"],
                //     @$item["compiled"]["lon"]
                // )
            }, $main_search_result);
            
        }

        $this->response->body(json_encode($main_search_coords));

    }
}
