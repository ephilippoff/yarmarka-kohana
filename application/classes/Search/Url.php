<?php defined('SYSPATH') OR die('No direct script access.');

class Search_Url
{
    static $reserved_segments = "/order_|page_|limit_|s_/";

    function __construct($uri = '', $get_params = array()) {
        $this->_reserved = array();
        $this->_uri = $this->save_and_clean_reserved($uri);
        $this->_category = self::get_category_in_uri($this->_uri);
        $this->_proper_category_uri = NULL;

        if (!$this->_category) {
            throw new Exception("Category not founded", $uri);
        }
        $this->_proper_category_uri = self::get_uri_category_segment($this->_category->id);
    }

    public function save_and_clean_reserved($uri = '') {
        $segments = explode("/", $uri);
        $result = array();
        $reserved = array();
        foreach ($segments as $key => $value) {
            if ( preg_match(self::$reserved_segments, $value) ) {
                array_push($reserved, strtolower($value) );
            } else {
                array_push($result, strtolower($value) );
            }
        }
        $this->_reserved = $reserved;
        return implode("/", $result);
    }

    public function is_seo_category_segment_incorrect() {
        $proper_uri = $this->_proper_category_uri;
        $this->_proper_category_uri = $proper_uri;
        if (strrpos($this->_uri, $proper_uri) === FALSE) {
            return $proper_uri;
        }
        return FALSE;
    }

    public function is_seo_param_segment_incorrect() {
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

    public function get_proper_category_uri() {
        return $this->_proper_category_uri;
    }

    /**
     * [get_category_and_seo_in_uri description]
     * @param  string $uri like '/avtotransport/mototsikly-velosipedy/mopedi-skuteri/bmw/x-5'
     * @return [array]      [description]
     */
    public function get_category_and_seo_in_uri($uri = '') {
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

    public static function get_seo_param_in_uri($category_id, $uri = '') {
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
                            ->where("reference.category", "=", (int) $category_id );;
            
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
    
    public static function get_category_in_uri($uri = '') {
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

    public static function get_category_segment_full($category_id) {
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

    public static function get_uri_category_segment($category_id) {
        $uri = array();
        $segments = self::get_category_segment_full($category_id);
        foreach ($segments as $segment) {
            array_push($uri, $segment->seo_name);
        }
        return implode("/", $uri);
    }

    public static function get_category_crubms($category_id) {
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

    public static function get_seo_param_segment($element_id) {
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

}