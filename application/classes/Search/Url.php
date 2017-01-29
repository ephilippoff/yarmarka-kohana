<?php defined('SYSPATH') OR die('No direct script access.');

class Search_Url
{

    static $reserved_query_params = array("photo", "video", "org", "private", "source", "user_id", "limit", "order", "page", "search", "period");
    static $reserved_query_params_requirements = array(
        "period" => array(1,2,3),
        "order" => array("price","price_desc","date_desc","date"),
        "limit" => array(5,10,15,20,25,30,50, 60,90)
    );
    static $reserved_query_params_defaults = array(
        "order" => "date_created",
        "limit" => 25,
        "page" => 0
    );
    static $reserved = array("order", "page", "limit");

    public function __construct($uri = '', $query_params = array(), $city_id = FALSE)
    {
        $this->_uri = $uri;
        $this->_category = self::get_category_in_uri($this->_uri);
        if (!$this->_category) {
            throw new Kohana_Exception("Category not found ");
        }
        $this->_category_childs = ORM::factory('Category')
                                ->get_childs(array($this->_category->id), TRUE)
                                ->getprepared_all();
                           
        $this->_proper_category_uri = $this->_category->url;

        $this->_seo_param = NULL;
        $this->_seo_filters = array();
        $seo_params_uri = trim(str_replace($this->_proper_category_uri, "", $this->_uri), "/");
        if ($seo_params_uri AND $seo_params_uri <> "") {
            $this->_seo_param = self::get_seo_param_in_uri($this->_category->id, $city_id, $seo_params_uri);
            if ($this->_seo_param) {
                $this->_seo_filters = $this->seo_params_to_query_params($this->_seo_param);
            }
        }

        $this->_query_params = $this->clean_query_params($this->_category->id, $query_params);
        $this->_reserved_query_params = $this->clean_reserved_query_params($query_params);

        $this->incorrectly_query_params_for_seo = FALSE;
    }

    public function seo_params_to_query_params($seo_param) {
        $result = array();
       
        if ($seo_param->parent_element) {
            $parent_ae = ORM::factory('Attribute_Element')
                            ->select("attribute_element.*", array("attribute.seo_name","attribute_seo_name"))
                            ->join('attribute')
                                ->on("attribute_element.attribute","=","attribute.id")
                            ->where("attribute_element.id","=",$seo_param->parent_element)
                            ->find();
            if ($parent_ae->loaded()) {
                $result[$parent_ae->attribute_seo_name] = $parent_ae->id;
            }
        }
        $result[$seo_param->attribute_seo_name] = $seo_param->id;
        return $result;
    }

    /**
     * [check_uri_segments description]
     * test application/tests/classes/searchRedirectTest.php
     * @return [void]
     */
    public function check_uri_segments()
    {
        if ($proper_category_uri = $this->is_seo_category_segment_incorrect()) {
            //TODO Log incorrect category seo

            // throw new Kohana_Exception_Withparams("category incorrect", array(
            //     "uri" => $proper_category_uri,
            //     "code" => 301
            // ));
            throw new HTTP_Exception_404;
        }

        if ($proper_seo_param_uri = $this->is_seo_param_segment_incorrect()) {
            //TODO Log incorrect seo params
            // if ($proper_seo_param_uri === TRUE) {
            //     throw new Kohana_Exception_Withparams("seo_param incorrect", array(
            //         "uri" => $this->get_proper_category_uri(),
            //         "code" => 301
            //     ));
            // } else {
            //     throw new Kohana_Exception_Withparams("seo_param correct, but uri wrong", array(
            //         "uri" => $this->get_proper_category_uri()."/".$proper_seo_param_uri,
            //         "code" => 301
            //     ));
            // }
            throw new HTTP_Exception_404;
        }

        if ($this->get_reserved_query_params("page")) {
            //TODO Log incorrect seo params
            if ($this->is_page_incorrect($this->get_reserved_query_params("page"))) {
                throw new Kohana_Exception_Withparams("reserved param `page` incorrect", array(
                    "uri" => $this->get_proper_segments(),
                    "code" => 301
                ));
            }
        }

        if ($old_param = $this->get_old_seo_query_param()) {
            if ($this->get_seo_param_segment($old_param)) {
                throw new Kohana_Exception_Withparams("old seo param incorrect",array(
                    "uri" => $this->get_proper_category_uri()."/".$this->get_seo_param_segment($old_param),
                    "code" => 301
                ));
            }
        }
    }

    public function check_query_params($query_params_from_source = array())
    {   
        $cleaned1_source_query_params = $this->clean_level1_query_params($query_params_from_source);
        $cleaned2_query_params = $this->clean_level2_query_params( $this->get_query_params() );
        if (count($cleaned1_source_query_params) > 0 
                AND $cleaned1_source_query_params !==  $cleaned2_query_params) {
            throw new Kohana_Exception_Withparams("query params incorrect");
        }
    }

    public function get_proper_segments() {
        $proper_category_uri = $this->get_proper_category_uri();
        $proper_seo_param_uri = $this->get_proper_seo_param_uri();

        return $proper_category_uri.( ($proper_seo_param_uri) ? "/".$proper_seo_param_uri: "" );
    }

    public function get_category_childs($direct_childs = FALSE)
    {
        $category_id = $this->_category->id;
        if ($direct_childs) {
            return array_filter($this->_category_childs, function($value) use ($category_id) {
                return  $category_id == $value->parent_id;
            });
        }
        return $this->_category_childs;
    }

    public function get_category_childs_id($direct_childs = FALSE)
    {
        return array_map(function($value){
            return $value->id;
        }, $this->get_category_childs($direct_childs));
    }

    public function get_category()
    {
        return $this->_category;
    }

    public function get_uri()
    {
        return $this->_uri;
    }

    public function get_query_params($name = NULL)
    {
        if ($name) {
            $query_params = new Obj($this->_query_params);
            return $query_params->{$name};
        } else {
            return $this->_query_params;
        }
    }

    public function get_seo_filters()
    {
        return $this->_seo_filters;
    }

    public function get_reserved_query_params($name = NULL)
    {
        if ($name) {
            $query_params = new Obj($this->_reserved_query_params);
            return $query_params->{$name};
        } else {
            return $this->_reserved_query_params;
        }
    }

    public function get_proper_category_uri()
    {
        return $this->_proper_category_uri;
    }

    public function get_proper_seo_param_uri()
    {   if (property_exists($this, "_proper_seo_param_uri")) {
            return $this->_proper_seo_param_uri;
        } else {
            return "";
        }
    }

    public function get_clean_query_params()
    {
        return $this->clean_level2_query_params($this->_query_params);
    }

    public function get_old_seo_query_param()
    {
        $last_param = FALSE;
        foreach ($this->_query_params as $key => $param) {
            if (count($param["value"]) > 1) break;
            if ($param["attribute"]->is_seo_used) {
                    $last_param = @$param["value"][0];
            }
        }
        return $last_param;
    }

    public function clean_level1_query_params($params = array())
    {
        $result = array();
        foreach ($params as $param_key => $param_value) {
            if (is_array($param_value)){
                if (!array_key_exists("min", $param_value)) {
                    foreach ($param_value as $key => $item) {
                        $result[$param_key][$key] = (int) $item;
                    }
                } else {
                    foreach ($param_value as $key => $item) {
                        $result[$param_key][$key] = (float) $item;
                    }
                }
            } else {
                $result[$param_key] = array((int) $param_value);
            }
        }
        return $result;
    }

    
    public function clean_level2_query_params($params = array()) {
        $result = array();
        foreach ($params as $param_key => $param_value) {
            if (is_array($param_value["value"])){
                if (!array_key_exists("min", $param_value["value"])) {
                    $result[$param_key]  = array_unique($param_value["value"]);
                } else {
                    krsort($param_value["value"]);
                    foreach ($param_value["value"] as $value_key => $value_value) {
                        if ($value_value > 0) {
                            $result[$param_key][$value_key] = $value_value;
                        }
                    }
                }
            } else {
                if (!$param_value["value"]) continue;
                 $result[$param_key]  = $param_value["value"];
                
            }
        }
        return $result;
    }

    public static function clean_query_params($category_id, $params = array())
    {
        $result = array();
        $keys = array();
        foreach (array_keys($params) as $key_item) {
            array_push($keys, (string) $key_item);
        }
        if (count($keys) > 0) {

            $attributes = ORM::factory('Attribute')
                            ->select(DB::expr("attribute.*"),"reference.is_seo_used")
                            ->join("reference")
                                ->on("reference.attribute","=","attribute.id")
                            ->where("reference.category","=",$category_id)
                            ->where("attribute.seo_name","IN",$keys)
                            ->order_by("reference.weight")
                            ->getprepared_all();

            foreach ($attributes as $attribute) {
                $value = $params[$attribute->seo_name];
                if (is_array($value)) {
                    if (!array_key_exists("min", $value)) {
                        $value  = array_unique($value);
                        foreach ($value as $key => $item) {
                            $value[$key] = (int) $item;
                        }
                    } else {
                        krsort($value);
                        foreach ($value as $key => $item) {
                            $value[$key] = (float) $item;
                        }
                    }
                } else {
                    if ($attribute->type == "list") {
                        $value = array((int) $value);
                    } else {
                        $value = trim(mb_strtolower($value));
                    }
                    
                }
                $result[$attribute->seo_name] = array(
                    "value" => $value,
                    "attribute" => $attribute
                );
            }
        }
        return $result;
    }

    public static function clean_reserved_query_params($get_params = array()) {
        $result = array();
        $reserved_query_params_requirements = new Obj(self::$reserved_query_params_requirements);
        $reserved_query_params_defaults = new Obj(self::$reserved_query_params_defaults);
        foreach ($get_params as $key => $value) {
            if (in_array($key, self::$reserved_query_params)){
                if ($reserved_query_params_requirements->{$key}) {
                    if ($key == "order") {
                        if ( in_array((string) $value, $reserved_query_params_requirements->{$key}) ) {
                            $result[$key] = (string) $value;
                        }
                    } else {
                        if ( in_array((int) $value, $reserved_query_params_requirements->{$key}) ) {
                            $result[$key] = (int) $value;
                        }
                    }
                } else {
                    if  ($key == "search") {
                        $result[$key] = (string) $value;
                    } else {
                        $result[$key] = (int) $value;
                    }
                    
                }
            }
        }

        foreach (self::$reserved_query_params as $param) {
            if (!isset($result[$param]) and $reserved_query_params_defaults->{$param}) {
                $result[$param] = $reserved_query_params_defaults->{$param};
            }
        }
        return $result;
    }

    public function is_seo_category_segment_incorrect()
    {
        $proper_uri = $this->_proper_category_uri;
        $this->_proper_category_uri = $proper_uri;
        if (strrpos($this->_uri, $proper_uri) === FALSE) {
            return $proper_uri;
        }
        return FALSE;
    }

    public function is_seo_param_segment_incorrect()
    {
        $seo_params_uri = trim(str_replace($this->_proper_category_uri, "", $this->_uri), "/");
        if ($seo_params_uri AND $seo_params_uri <> "") {
            $element = $this->_seo_param;
            if ($element) {
                $proper_seo_param = self::get_seo_param_segment($element->id);
                $this->_proper_seo_param_uri = $proper_seo_param;

                if ($seo_params_uri <> $proper_seo_param) {
                    return  $proper_seo_param;
                } else {
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    // public function get_category_and_seo_in_uri($uri = '')
    // {
    //     $seo_param = NULL;
    //     $category_in_uri = self::get_category_in_uri($uri);
    //     if ($category_in_uri) {
    //         $category_uri = $category_in_uri->url;
    //         $seo_params_uri = trim(str_replace($category_uri, "", $uri), "/");
    //         if ($seo_params_uri) {
    //             $seo_param = self::get_seo_param_in_uri($category_in_uri->id, $city_id, $seo_params_uri);
    //         }
    //     }
    //     return array(
    //         "category" => $category_in_uri,
    //         "seo_param" => $seo_param
    //     );
    // }

    public static function get_seo_param_in_uri($category_id, $city_id, $uri = '')
    {
        $ae = NULL;
        $uri = explode("/", $uri);
        $uri = array_reverse($uri);
        $_parent_ae = NULL;
        $_ae_id = array();

        foreach ($uri as $key => $value) {

            $_ae = ORM::factory('Attribute_Element')
                        ->get_elements($category_id);

            $_ae = $_ae->where("attribute_element.seo_name", "=", strtolower($value) );
            if ($_parent_ae) {
                $_ae = $_ae->where("attribute_element.parent_element", "=", $_ae_id);
            }

            $_ae->cached(Date::DAY)->find();

            if ($_ae->loaded()) {
                $_parent_ae = $_ae->parent_element;
                $_ae_id = $_ae->id;
                $ae = $_ae->get_row_as_obj();
            } 
        }
        return $ae;
    }

    public static function get_category_childs_elements($category_id, $city_id, $seo_filters = array())
    {
        $seo_filters_values = array_values($seo_filters);
        $parent_element_id = end($seo_filters_values);

        $elements = ORM::factory('Attribute_Element')
                        ->get_elements_with_published_objects($category_id, $city_id);

        if ($parent_element_id) {
            $elements = $elements->where("attribute_element.parent_element","=",$parent_element_id);
        } else {
            $elements = $elements->is_null("attribute.parent");
        }
        $elements = $elements->order_by("attribute_element.title");
        $elements = $elements->getprepared_all();

        foreach ($elements as $element) {
            $element->url = Search_Url::get_seo_param_segment($element->id);
        }
        

        return $elements;
    }
    
    public static function get_category_in_uri($uri = '')
    {
        $category = NULL;
        $_uri = explode("/", $uri);
        $_uri = array_reverse($_uri);
        $_parent_category_id = NULL;
        $_category_ids = array();

        foreach ($_uri as $key => $value) {
            $_category = ORM::factory('Category')
                            ->where("seo_name", "=", $value);
            
            if ($_parent_category_id) {
                $_category = $_category->where("parent_id", "in", $_category_ids);
            }

            $_category->cached(Date::WEEK)->find();

            if ($_category->loaded()) {
                $_parent_category_id = $_category->parent_id;
                array_push($_category_ids, $_category->id);
                $category = $_category->get_row_as_obj();
            } 
        }

        return $category;
    }

    public static function get_category_segment_full($category_id)
    {
       $result = array();
       $category = ORM::factory('Category', $category_id);
       if (!$category->loaded()) return "";

       $parent_category_id = $category->parent_id;
       array_push($result, $category);
       while (1== 1) {
            if ($parent_category_id == 1 OR $parent_category_id == NULL) {
                break;
            }
            $parent = ORM::factory('Category', $parent_category_id);
            if ($parent->loaded()) {
                $parent_category_id = $parent->parent_id;
                array_push($result, $parent);
            } else {
                $parent_category_id = NULL;
            }
       }
       $result = array_reverse($result);

       return $result;
    }

    public static function get_uri_category_segment($category_id)
    {
        $uri = array();
        $segments = self::get_category_segment_full($category_id);
        foreach ($segments as $segment) {
            array_push($uri, $segment->seo_name);
        }
        return implode("/", $uri);
    }

    public static function get_category_crubms($category_id, $query = '')
    {
        $crumbs = array();
        $segments = self::get_category_segment_full($category_id);
        foreach ($segments as $segment) {
            array_push($crumbs, array(
                "id" => $segment->id,
                "title" => $segment->title,
                "seo_name" => $segment->seo_name,
                "uri" => $segment->url,
                "query" => $query
            ));
        }
        return $crumbs;
    }

    public static function get_seo_elements_crubms($seo_filters, $category_url)
    {
        $crumbs = array();
        if (count($seo_filters) < 2) return $crumbs;
        $seo_filters_values = array_values($seo_filters);
        $first_element_id = reset($seo_filters_values);

        
        $segments = self::get_seo_param_segment_full($first_element_id);
        $crumburi = array();
        foreach ($segments as $segment) {
            array_push($crumburi, $segment->seo_name);
            array_push($crumbs, array(
                "id" => $segment->id,
                "title" => $segment->title,
                "seo_name" => $segment->seo_name,
                "uri" => $category_url."/".implode("/", $crumburi)
            ));
        }
        return $crumbs;
    }

    public static function get_seo_param_segment_full($element_id)
    {
        $result = array();
        $element = ORM::factory('Attribute_Element', $element_id);
        if (!$element->loaded()) return array();

        $parent_element_id = $element->parent_element;
        array_push($result, $element);
        while (1== 1) {
             if ($parent_element_id == NULL) {
                 break;
             }
             $parent = ORM::factory('Attribute_Element', $parent_element_id);
             if ($parent->loaded()) {
                 $parent_element_id = $parent->parent_element;
                 array_push($result, $parent);
             } else {
                 $parent_element_id = NULL;
             }
        }
        return array_reverse($result);
    }

    public static function get_seo_param_segment($element_id)
    {
       $segments = self::get_seo_param_segment_full($element_id);

       $uri = array_map(function($value){
            return $value->seo_name;
        }, $segments);

       return implode("/", $uri);
    }


    public static function is_page_incorrect($value)
    {
        if ($value == 1) {
            return TRUE;
        }
        return FALSE;
    }

    public static function getcounters($host, $category_url, $categories = array())
    {
        return ORM::factory('Search_Url_Cache')->get_count_for_categories($host, $category_url, $categories);
    }

    public static function set_count_for_categories($host, &$categories, $current_category = FALSE) {

        $count_for_categories = self::getcounters($host, (($current_category) ? $current_category : '') , $categories);

        return array_map(function($category) use($host, $count_for_categories, $current_category) {
            $url = ($current_category) ? sprintf('%s/%s/%s', $host, $current_category, $category->url) : sprintf('%s/%s', $host, $category->url);

            
            if (array_key_exists($url, $count_for_categories)) {
                $category->count = $count_for_categories[$url];
            } else {
                $category->count = 0;
            }

        }, $categories);

    }

    public static function sort_categories(&$categories, $by = 'by_weight') {

        usort($categories, function($a, $b) use ( $by)
        {
            
            //1. by count desc

            if ( $by == 'by_count') {
                $count_a = (int) $a->count;
                $count_b = (int) $b->count;
                
                if ($count_a != $count_b) {
                    return $count_b - $count_a;
                }
            }
            
            //2. by title

            if ( $by == 'by_title') {
                $title_a = $a->title;
                $title_b = $b->title;
                
                if ($title_a != $title_b) {
                    return $title_a < $title_b ? -1 : 1;
                }
            }
            

            //3. by weight
            $weight_a = (int) $a->weight;
            $weight_b = (int) $b->weight;
            
            return $weight_a < $weight_b ? -1: 1;
            
        });

        return $categories;
    }

    public static function clean_empty_category_childs_elements($categories, $counters, $url)
    {
        $result = array();
        $counters = new Obj($counters);
        foreach ($categories as $category) {
            if (isset($counters->{$url."/".$category->url}) AND $counters->{$url."/".$category->url} == 0) continue;
            $result[] = $category;
        }
        return $result;
    }

    public static function get_query_params_without_reserved($query_params = array(), $set_params = array(), $unset_params = array())
    {
       
        $result = array();
        $reserved = self::$reserved;
        foreach ($query_params as $key => $value) {
            if (!in_array($key, $reserved)){
                $result[$key] = $value;
            }
        }
        foreach ($set_params as $key => $value) {
            $result[$key] = $value;
        }

        foreach ($unset_params as $value) {
            if (isset($result[$value])) {
                unset($result[$value]);
            }
        }
        ksort($result);
        return $result;
    }

    public static function get_suri_without_reserved($query_params = array(), $set_params = array(), $unset_params = array())
    {
        $query_params = self::get_query_params_without_reserved($query_params, $set_params, $unset_params);
        $result = http_build_query($query_params);
        $result = ($result) ? "?".$result : "";
        return $result;
    }
}
