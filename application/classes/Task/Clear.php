<?php defined('SYSPATH') or die('No direct script access.');


class Task_Clear extends Minion_Task
{
    protected $_options = array(
        'seo' => 0
    );

    protected function _execute(array $params)
    {
        $seo  = $params['seo'];
        if ($seo) {
            Minion_CLI::write('set_seo_names');
            self::set_attribute_element_seo_name($seo);
            return;
        }

        Minion_CLI::write('set_category_urls');
        $this->set_category_urls();
        
        Minion_CLI::write('set_attribute_element_urls');
        $this->set_attribute_element_urls();
        
        Minion_CLI::write('clear_search_url_cache');
        $this->clear_search_url_cache();

        Minion_CLI::write('save_object_statistic');
        $this->save_object_statistic();
        
    }

    function save_object_statistic() {

        $category_array = array();

        $category_list = ORM::factory('Category')
                                ->where("through_weight","IS NOT",NULL)
                                ->where("is_ready", "=", 1)
                                ->order_by("through_weight")
                                ->cached(Date::WEEK, array("category", "add"))
                                ->find_all();
        foreach ($category_list as $item) {
            
            $childs = ORM::factory('Category')
                ->where("parent_id","=",$item->id)
                ->where("is_ready", "=", 1)
                ->order_by("weight")
                //->cached(DATE::WEEK, array("category", "add"))
                ->find_all();

            if (count($childs)>0 AND $item->id <> 1)
            {
                $childs_array = array();
                foreach ($childs as $child) {
                    if (!ORM::factory('Category')
                            ->where("parent_id","=",$child->id)
                            ->where("is_ready", "=", 1)
                            ->count_all(NULL, Date::WEEK))
                    {
                        array_push($category_array, $child->id);
                    }
                }

            }
            
        }

        array_push($category_array, 42);
        array_push($category_array, 156);
        array_push($category_array, 72);

        $filters = array(
            array('number','IS', NULL),
            array('real_date_created','>',DB::expr("CURRENT_DATE + interval '1 hour'")),
            array('category','IN', $category_array)
        );



        $objects_by_category =  Statistic::get_new_objects_category( 'day', $filters);




        echo Debug::vars($objects_by_category);

        $cities = ORM::factory('City')->where('is_visible','=', 1)->find_and_map(function($row) {
            return $row->id;
        });


         $filters = array(
            array('number','IS', NULL),
            array('real_date_created','>',DB::expr("CURRENT_DATE + interval '1 hour'")),
            array('city_id','IN', $cities)
        );


        $objects_by_city =  Statistic::get_new_objects_city( 'day', $filters);

        $stat = ORM::factory('Object_StatisticAll');

        $stat->period = DB::expr('NOW()');
        $stat->statdata = serialize(array(
            'objects_by_city' => $objects_by_city,
            'objects_by_category' => $objects_by_category
        ));

        $stat->save();

        echo Debug::vars($objects_by_city);


    }

    function clear_search_url_cache()
    {
        ORM::factory('Search_Url_Cache')
                ->where("created_on","<=", DB::expr("CURRENT_TIMESTAMP-INTERVAL '7 days'"))
                ->delete_all();
    }

    function set_category_urls()
    {
        $categories = ORM::factory('Category')->find_all();
        foreach ($categories as $category) {
            $url = Search_Url::get_uri_category_segment($category->id);
            $_category = ORM::factory('Category',$category->id);
            $_category->url = $url;
            $_category->save();
        }
        
    }

    static function set_attribute_element_seo_name($id)
    {
        $aes = ORM::factory('Attribute_Element')
            ->where("attribute","=", $id)
            ->find_all();

        foreach ($aes as $ae) {
            $seo_name = $ae->title;
            $seo_name = Text::rus2translit($seo_name);
            $seo_name = Text::clear_symbols_for_seo_name($seo_name);
            $seo_name = mb_strtolower($seo_name);
            $seo_name =  str_replace(array(' '), '-', $seo_name);
            $cat = ORM::factory('Category')
                    ->where("seo_name","=", $seo_name);
            $i = 1;
            while ($cat->find()->loaded()) {

                $cat = ORM::factory('Category')
                    ->where("seo_name","=", $seo_name.$i);

                $i++;
            }
            if ($i > 1) {
                $seo_name = $seo_name.$i;
            }

            $k = 1;
            $cat = ORM::factory('Attribute_Element')
                    ->where("attribute","<>", $id)
                    ->where("seo_name","=", $seo_name);

            while ($cat->find()->loaded()) {

                $cat = ORM::factory('Attribute_Element')
                    ->where("attribute","<>", $id)
                    ->where("seo_name","=", $seo_name.$k);

                $k++;
            }

            if ($k > 1) {
                $seo_name = $seo_name.$i;
            }

            $ae->seo_name = $seo_name;
            $ae->save();
            //Minion_CLI::write($seo_name);
        }
    
    }

    static function set_attribute_element_urls($attribute = NULL)
    {

        $aes = ORM::factory('Attribute_Element');

        if ($attribute) {
            $aes = $aes->where("attribute","=", $attribute);
        }

         $aes = $aes->find_all();
        foreach ($aes as $ae) {
            $url = Search_Url::get_seo_param_segment($ae->id);
            $ae->url = $url;
            $ae->save();
        }
        
    }

    static function sitemap()
    {
        $twig = Twig::factory('other/sitemap');
        $entries = array();
        $sitemaps = array();
        $cities_modified = DB::select(array("city.seo_name", "url") , DB::select("cat_obj.date_created")
                                            ->from(array("object", "cat_obj"))
                                            ->where("cat_obj.active","=","1")
                                            ->where("cat_obj.is_published","=","1")
                                            ->where("cat_obj.city_id","=", DB::expr("city.id") )
                                            ->order_by("date_created", "desc")
                                            ->limit(1)
                        )->from("city")->where("is_visible","=",1)->execute()->as_array();

        $cities_modified = array_combine ( 
            array_map(function($item){ return $item["url"]; }, $cities_modified), 
            array_map(function($item){ return @$item["date_created"]; }, $cities_modified)
        );

        $cities = ORM::factory('City')->where("seo_name","=","surgut")->find_all();

        foreach ($cities as $city) {

            $entry = array(
                "loc" => "http://".$city->seo_name.".yarmarka.biz",
                "changefreq" => "daily",
                "priority" => "0.8"
            );

            if (isset($cities_modified[$city->seo_name]) AND $cities_modified[$city->seo_name]) {
                $entry["lastmod"] = date('c', strtotime($cities_modified[$city->seo_name]));
            }

            $entries[] = $entry;
        }

        $twig->entries = $entries;
        $filedata = (string) $twig;
        $filename = APPPATH."../sitemaps/cities.sitemap.xml";
        file_put_contents($filename, $filedata);
        chmod ($filename, 0777);

        foreach ($cities as $city) {
            $city_name = $city->seo_name;
            $filename = APPPATH."../sitemaps/".$city_name.".sitemap.xml";
            if (!file_exists($filename)) continue;

            $sitemap = array(
                "loc" => "http://".$city->seo_name.".yarmarka.biz/sitemap.xml",
            );

            if (isset($cities_modified[$city->seo_name]) AND $cities_modified[$city->seo_name]) {
                $sitemap["lastmod"] = date('c', strtotime($cities_modified[$city->seo_name]));
            }

            $sitemaps[] = $sitemap;
        }
        // $sitemaps[] = array(
        //     "loc" => "http://yarmarka.biz/cities.sitemap.xml",
        //     "changefreq" => "daily",
        //     "priority" => "0.8"
        // );
        $twig->sitemaps = $sitemaps;
        $filedata = (string) $twig;
        $filename = APPPATH."../sitemaps/sitemap.xml";

        file_put_contents($filename, $filedata);
        chmod ($filename, 0777);
    }

    static function sitemap_by_city($city_name)
    {
        $config = Kohana::$config->load("common");
        $main_domain = $config["main_domain"];

        $twig = Twig::factory('other/sitemap');
        $entries = array();
        $city = ORM::factory('City')->where("seo_name","=", $city_name)->find();
        
        $categories_modified = DB::select("category.url" , DB::select("cat_obj.date_created")
                                            ->from(array("object", "cat_obj"))
                                            ->where("cat_obj.active","=","1")
                                            ->where("cat_obj.is_published","=","1")
                                            ->where("cat_obj.category","=", DB::expr("category.id") )
                                            ->where("cat_obj.city_id","=", $city->id )
                                            ->order_by("date_created", "desc")
                                            ->limit(1)
                        )->from("category")
                        ->execute()
                        ->as_array();

        $categories_modified = array_combine ( 
            array_map(function($item){ return $item["url"]; }, $categories_modified), 
            array_map(function($item){ return @$item["date_created"]; }, $categories_modified)
        );

        $categories = ORM::factory('Category')
                        ->where("is_ready", "=", 1)
                        ->order_by("url")
                        ->find_all();
        $categories_ids = array();
        foreach ($categories as $category) {
            if (!$category->url) continue;
            $categories_ids[] = $category->id;

            $entry = array(
                "loc" => "http://".$city_name.".".$main_domain."/".$category->url,
                "changefreq" => "daily",
                "priority" => "0.8"
            );

            if (isset($categories_modified[$category->url]) AND $categories_modified[$category->url]) {
                $entry["lastmod"] = date('c', strtotime($categories_modified[$category->url]));
            } else {
                $entry["lastmod"] = FALSE;
            }
            $entries[] = $entry;

            $elements = ORM::factory('Attribute_Element')
                            ->get_elements_with_published_objects($category->id, $city->id)
                            ->cached(Date::DAY)
                            ->find_all();

            foreach ($elements as $element) {
                if (!$element->url) continue;
                $entry_element = array(
                    "loc" => "http://".$city_name.".".$main_domain."/".$category->url."/".$element->url,
                    "changefreq" => "daily",
                    "priority" => "0.8"
                );
                if ($entry["lastmod"]) {
                    $entry_element["lastmod"] = $entry["lastmod"];
                }
                $entries[] = $entry_element;
            }

            
        }

        $objects = Search::searchquery(
            array(
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $city->id,
                "category_id" => $categories_ids,
            ),
            array("limit" => 15000)
        );

        $objects = Search::getresult($objects->execute()->as_array());
        foreach ($objects as $object) {
            if (!isset($object["compiled"]["url"])) continue;
            $entry_object = array(
                "loc" => $object["compiled"]["url"],
                "changefreq" => "weekly",
                "priority" => "0.5",
                "lastmod" => ( strtotime($object["date_updated"]) > strtotime($object["date_created"]) ) ? date('c', strtotime($object["date_updated"])) : date('c', strtotime($object["date_created"]))
            );
            $entries[] = $entry_object;
        }

        $twig->entries = $entries;
        $filedata = (string) $twig;

        $filename = APPPATH."../sitemaps/$city_name.sitemap.xml";

        file_put_contents($filename, $filedata);
        chmod ($filename, 0777);
    }
    
}