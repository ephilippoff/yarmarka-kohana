<?php defined('SYSPATH') OR die('No direct script access.');

class Domain 
{
    static $reserved_segments = array("c");

    public function __construct() {
        $this->_domain = URL::SERVER('HTTP_HOST');
        
        $config = Kohana::$config->load("common");
        $this->_main_domain = $config["main_domain"];
        $this->_main_category = $config["main_category"];

        $this->_subdomain = strtolower(trim( str_replace($this->_main_domain, "", $this->_domain), "."));
        $this->_city = NULL;
        $this->_reserved_domain = NULL;
        

        return $this;
    }

    private static function get_city_by_subdomain($subdomain) {
        $city = ORM::factory('City')
                ->where("seo_name", "=", $subdomain)
                ->find();

        if ($city->loaded()) {
            return $city->get_row_as_obj();
        }
        return FALSE;
    }

    public function is_domain_incorrect() {
        if ( $this->_subdomain  AND in_array($this->_subdomain, self::$reserved_segments) ) {
            $this->_reserved_domain = $this->_subdomain;
            return FALSE;
        } else if ( $this->_subdomain  AND $city = self::get_city_by_subdomain($this->_subdomain)) {
            $this->_city = $city;
            return FALSE;
        } else if ( $this->_subdomain) {
            return $this->_main_domain;
        } else {
            return FALSE;
        }
    }

    public function get_city() {
        return $this->_city;
    }

    public function get_domain() {
        return $this->_domain;
    }

    public function get_subdomain() {
        return $this->_subdomain;
    }

    public function get_main_category() {
        return $this->_main_category;
    }

    public static function get_domain_by_city($domain_str, $url_str, $protocol_str = "http://")
    {
        $config = Kohana::$config->load("common");
        $main_domain = $config["main_domain"];
        if ($domain_str) {
            $domain_str .= ".";
        }
        if (!$url_str) {
            return $protocol_str.$domain_str.$main_domain;
        } else {
            return $protocol_str.$domain_str.$main_domain.self::url($url_str);
        }
    }
    
    public static function url($link)
    {
        return "/".$link;
    }
}