<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Special controller for internal sub requests (HMVC)
 * 
 * @uses Controller
 * @uses _Template
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Controller_Block_Twig extends Controller_Block
{

    public function before()
    {
        parent::before();
        $this->auto_render = FALSE;
    }

    public function action_topline()
    {
        $twig = Twig::factory('block/header/topline');
        $twig->user = Auth::instance()->get_user();
        $twig->favourites = $this->request->post("favourites");
        $this->response->body($twig);
    }

    public function action_logoline()
    {
        $twig = Twig::factory('block/header/logoline');
        $this->response->body($twig);
    }

    public function action_adslinkline()
    {
        $city_id = $this->request->post("city_id");
        $category_id = $this->request->post("category_id");
        $theme_class = $this->request->post("theme_class");

        $twig = Twig::factory('block/header/adslinkline');
        $twig->imagelinks = $this->adslinkline($city_id, $category_id, "image");
        $twig->textlinks = $this->adslinkline($city_id, $category_id, "text");
        $twig->theme_class = $theme_class;
        $twig->info_link = "/ourservices/kontekstnaya-reklama";
        $this->response->body($twig);
    }

    public function action_othercities()
    {
        $city_id = $this->request->post("city_id");
        $twig = Twig::factory('block/menu/city');

        $cities = ORM::factory('City')->where("is_visible","=",1);
        if ($city_id)
            $cities = $cities->where("id","<>",$city_id);
        $twig->cities = $cities->getprepared_all();
        $this->response->body($twig);
    }

    public function action_links()
    {
        $city_id = $this->request->post("city_id");
        $twig = Twig::factory('block/links');

        $links = array(
            "/" => "Главная",
            "/search_category" => "Все объявления",
            "/rabota/vakansii" => "Вакансии города"
        );

        $twig->links = $links;
        $this->response->body($twig);
    }

    public function action_mainmenu()
    {
        $city_id = $this->request->post("city_id");
        $twig = Twig::factory('block/menu/main');

        $categories = ORM::factory('Category')->get_categories_extend(array(
            "with_child" => TRUE, 
            "with_ads" => TRUE, 
            "city_id" => $city_id
        ));
        
        $twig->categories1l = $categories["main"];
        $twig->categories2l = $categories["childs"];
        $twig->parents_ids  = $categories["main_ids"];
        $twig->banners      = $categories["banners"];

        $this->response->body($twig);
    }

    ////// Реализация содержимого блоков

    public function adslinkline($city_id = NULL, $category_id = NULL, $type = "image")
    {
        $reklama = ORM::factory('Reklama')
                        ->where(DB::expr('CURRENT_DATE'), '>=', DB::expr('start_date') )
                        ->where(DB::expr('CURRENT_DATE'), '<=', DB::expr('end_date') )
                        ->where('active', '=',  1);
        if ($city_id) {
             $reklama =  $reklama->where(DB::expr((int) $city_id), "=", DB::expr("ANY(cities)") );
        }

        if ($category_id) {
             $reklama =  $reklama->where(DB::expr((int) $category_id), "=", DB::expr("ANY(categories)"));
        }

        $types = array(
            "text" => array(1),
            "image" => array(2,3)
        );

        $reklama =  $reklama->where("type", "IN", $types[$type]);

        return $reklama->getprepared_all();
    }

    public static function kupon_categories($params = NULL)
    {
        
        $elements = array();

        $domain = new Domain();
        $city = $domain->get_city_by_subdomain($domain->get_subdomain());

        $category_name = "kupony";
        $category =  ORM::factory('Category')
                        ->where("seo_name","=",$category_name)
                        ->cached(Date::WEEK)
                        ->find();

        if (!$category->loaded())
        {
            return $elements;
        }

        $_elements = ORM::factory('Attribute_Element')
                        ->get_elements_with_published_objects($category->id, ($city) ? $city->id : NULL)
                        ->select("attribute_element.*", array("attribute.seo_name","attribute_seo_name"), array("category.url","category_url"))
                        ->join('category')
                            ->on("reference.category","=","category.id")
                        ->cached(Date::DAY)
                        ->getprepared_all();

        foreach ($_elements as $item) {
            $item->attribute = true;
            $item->url = $item->seo_name;
            $elements[] = $item;
        }
        $link_counters = new Obj(Search_Url::getcounters($domain->get_domain(), $category_name, $elements ));
        foreach ($elements as $item) {
           $item->count = $link_counters->{$domain->get_domain()."/$category_name/".$item->url};
        }
        return $elements;
    }

    public static function news_categories($params = NULL)
    {
        
        $elements = array();

        $domain = new Domain();
        $city = $domain->get_city_by_subdomain($domain->get_subdomain());

        $category_name = "novosti";
        $category =  ORM::factory('Category')
                        ->where("seo_name","=",$category_name)
                        ->cached(Date::WEEK)
                        ->find();

        if (!$category->loaded())
        {
            return $elements;
        }

        $_elements = ORM::factory('Attribute_Element')
                        ->get_elements_with_published_objects($category->id, ($city) ? $city->id : NULL)
                        ->select("attribute_element.*", array("attribute.seo_name","attribute_seo_name"), array("category.url","category_url"))
                        ->join('category')
                            ->on("reference.category","=","category.id")
                        ->cached(Date::DAY)
                        ->getprepared_all();

        foreach ($_elements as $item) {
            $item->attribute = true;
            $item->url = $item->seo_name;
            $elements[] = $item;
        }
        $link_counters = new Obj(Search_Url::getcounters($domain->get_domain(), $category_name, $elements ));
        foreach ($elements as $item) {
           $item->count = $link_counters->{$domain->get_domain()."/$category_name/".$item->url};
        }

        return $elements;
    }

    public function action_kuponmenu()
    {
        $city_id = $this->request->post("city_id");
        $states = array(1);
        $cached = TRUE; 
        $banners = array();
        
        if (Auth::instance()->get_user() and in_array((int)Auth::instance()->get_user()->role, array(1, 9)))
        {
            $states = array(1, 2);
            $cached = FALSE;
        }       
        
        $banners_all = ORM::factory('Category')->get_banners($city_id, $states, $cached);

        $categories_banners = array_map(function($value) {
            return $value->category_id;
        }, $banners_all);
        
        if (count($banners_all) > 0) {
            $banners = array_combine($categories_banners, $banners_all);
        }
        
        $twig = Twig::factory('block/menu/kupon');      
        
        $twig->categories = self::kupon_categories();
        $twig->banners    = $banners;

        $this->response->body($twig);
    }
	
    public function action_newsmenu()
    {
        $city_id = $this->request->post("city_id");
        $states = array(1);
        $cached = TRUE; 
        $banners = array();
        
        if (Auth::instance()->get_user() and in_array((int)Auth::instance()->get_user()->role, array(1, 9)))
        {
            $states = array(1, 2);
            $cached = FALSE;
        }       
        
        $banners_all = ORM::factory('Category')->get_banners($city_id, $states, $cached);

        $categories_banners = array_map(function($value) {
            return $value->category_id;
        }, $banners_all);
        
        if (count($banners_all) > 0) {
            $banners = array_combine($categories_banners, $banners_all);
        }
        
        $twig = Twig::factory('block/menu/news');      
        
        $twig->categories = self::news_categories();
        $twig->banners    = $banners;

        $this->response->body($twig);
    }	

}