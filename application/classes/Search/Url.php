<?php defined('SYSPATH') OR die('No direct script access.');

class Search_Url
{
    static $reserved_segments = "/order_|page_|limit_/";
    static $reserved_requirements = array(
        "order" => array("price","price_desc","date_desc","date"),
        "limit" => array(5,10,15,20,25,30,60,90)
    );

    static $reserved_query_params = array("search_by_params");

    public function __construct($uri = '', $query_params = array())
    {
        $this->_uri = $this->clean_uri($uri);
        $this->_fulluri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->_uri_query_segment = urldecode(parse_url($this->_fulluri, PHP_URL_QUERY));
        $this->_reserved = $this->clean_reserved($uri);
        $this->_category = self::get_category_in_uri($this->_uri);
        $this->_query_params = $this->clean_query_params($this->_category->id, $query_params);
        $this->_proper_category_uri = NULL;

        if (!$this->_category) {
            throw new Exception("Category not found", $uri);
        }
        $this->_proper_category_uri = self::get_uri_category_segment($this->_category->id);
    }

    public function get_category()
    {
        return $this->_category;
    }

    public function get_reserved()
    {
        return $this->_reserved;
    }

    public function get_uri()
    {
        return $this->_uri;
    }

    public function get_proper_category_uri()
    {
        return $this->_proper_category_uri;
    }

    public function get_proper_seo_param_uri()
    {
        return $this->_proper_seo_param_uri;
    }

    public function get_query_params()
    {
        return $this->_query_params;
    }

    public function get_uri_query_segment()
    {
        return $this->_uri_query_segment;
    }

    public function get_old_seo_query_param()
    {
        $last_param = FALSE;
        foreach ($this->_query_params as $key => $param) {
            if ($param["attribute"]->is_seo_used AND count($param["value"]) == 1) {
                    $last_param = $param["value"][0];
            }
        }
        return $last_param;
    }

    public function clean_uri($uri = '')
    {
        $segments = explode("/", $uri);
        $result = array();
        foreach ($segments as $key => $value) {
            if ( !preg_match(self::$reserved_segments, $value) ) {
                array_push($result, strtolower($value) );
            }
        }
        return implode("/", $result);
    }

    public function clean_query_params($category_id, $params = array())
    {
        $result = array();
        if (count(array_keys($params)) > 0) {

            $attributes = ORM::factory('Attribute')
                            ->select(DB::expr("attribute.*"),"reference.is_seo_used")
                            ->join("reference")
                                ->on("reference.attribute","=","attribute.id")
                            ->where("reference.category","=",$category_id)
                            ->where("attribute.seo_name","IN",array_keys($params))
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
            $element = $this->_seo_param = self::get_seo_param_in_uri($this->_category->id, $seo_params_uri);
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
    
    public function get_category_and_seo_in_uri($uri = '')
    {
        $seo_param = NULL;
        $category_in_uri = self::get_category_in_uri($uri);
        if ($category_in_uri) {
            $category_uri = self::get_uri_category_segment($category_in_uri->id);
            $seo_params_uri = trim(str_replace($category_uri, "", $uri), "/");
            if ($seo_params_uri) {
                $seo_param = self::get_seo_param_in_uri($category_in_uri->id, $seo_params_uri);
            }
        }
        return array(
            "category" => $category_in_uri,
            "seo_param" => $seo_param
        );
    }

    public static function get_seo_param_in_uri($category_id, $uri = '')
    {
        $ae = NULL;
        $uri = explode("/", $uri);
        $uri = array_reverse($uri);
        $_parent_ae = NULL;
        $_ae_id = array();

        foreach ($uri as $key => $value) {
            $_ae = ORM::factory('Attribute_Element')
                            ->join('data_list')
                                ->on("data_list.value","=","attribute_element.id")
                            ->join('reference')
                                ->on("data_list.reference","=","reference.id")
                            ->where("seo_name", "=", strtolower($value) )
                            ->where("reference.category", "=", (int) $category_id )
                            ->where("reference.is_seo_used","=",1);
            
            if ($_parent_ae) {
                $_ae = $_ae->where("parent_element", "=", $_ae_id);
            }

            $_ae->find();

            if ($_ae->loaded()) {
                $_parent_ae = $_ae->parent_element;
                $_ae_id = $_ae->id;
                $ae = $_ae->get_row_as_obj();
            } 
        }
        return $ae;
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

            $_category->find();

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

    public static function get_category_crubms($category_id)
    {
        $crumbs = array();
        $segments = self::get_category_segment_full($category_id);
        $crumburi = array();
        foreach ($segments as $segment) {
            array_push($crumburi, $segment->seo_name);
            array_push($crumbs, array(
                "id" => $segment->id,
                "title" => $segment->title,
                "seo_name" => $segment->seo_name,
                "uri" => implode("/", $crumburi)
            ));
        }
        return $crumbs;
    }

    public function get_query_params_segment($params = array()) {
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
        return urldecode(http_build_query($result));
    }

    public static function get_seo_param_segment($element_id)
    {
       $uri = array();
       $element = ORM::factory('Attribute_Element', $element_id);
       if (!$element->loaded()) return "";

       $parent_element_id = $element->parent_element;
       array_push($uri, $element->seo_name);
       while (1== 1) {
            if ($parent_element_id == NULL) {
                break;
            }
            $parent = ORM::factory('Attribute_Element', $parent_element_id);
            if ($parent->loaded()) {
                $parent_element_id = $parent->parent_element;
                array_push($uri, $parent->seo_name);
            } else {
                $parent_element_id = NULL;
            }
       }
       $uri = array_reverse($uri);

       return implode("/", $uri);
    }

    public static function clean_reserved($uri = '') {
        $params = explode("/", $uri);
        $result = array();
        foreach ($params as $param)
        {
            if (preg_match("/page_([0-9]+)/", $param, $match))
            {
                $result["page"] = (int) $match[1];
            }
            if (preg_match("/limit_([0-9]+)/", $param, $match))
            {
                $result["limit"] = (int) $match[1];
            }
            if (preg_match("/order_([a-zA-Z0-9_\-]+)/", $param, $match))
            {
                $value = trim(strtolower($match[1]));
                $value_params = explode("_", $value);
                if (count($value_params) == 1) {
                    $result["order"] = $value_params[0];
                    $result["order_direction"] = "asc";
                } elseif (count($value_params) == 2) {
                    $result["order"] = $value_params[0];
                    $result["order_direction"] = "desc";
                }
            }
        }

        return $result;
    }

    public static function is_page_incorrect($value){
        if ($value == 1) {
            return TRUE;
        }
        return FALSE;
    }

    public static function is_order_incorrect($value){
        if (in_array($value, self::$reserved_requirements["order"])) {
            return FALSE;
        }
        return TRUE;
    }

    public static function is_limit_incorrect($value){
        if (in_array($value, self::$reserved_requirements["limit"])) {
            return FALSE;
        }
        return TRUE;
    }

}