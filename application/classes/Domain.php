<?php defined('SYSPATH') OR die('No direct script access.');

class Domain 
{
    static $reserved_segments = array("c");
    public $_city = NULL;
    public function __construct() {
        $this->_domain = URL::SERVER('HTTP_HOST');
        
        $config = Kohana::$config->load("common");
        $this->_main_domain = $config["main_domain"];
        $this->_main_category = $config["main_category"];

        $this->_subdomain = strtolower(trim( str_replace($this->_main_domain, "", $this->_domain), "."));
        $this->_city =  $this->get_city_by_subdomain($this->_subdomain);
        $this->_last_city_id = Cookie::get('location_city_id');
        $this->_reserved_domain = NULL;
        return $this;
    }

    public function init()
    {
        return $this->is_domain_incorrect();
    }

    public function get_city_by_subdomain($subdomain) {
        if ($subdomain == $this->_subdomain AND $this->_city) {
            return $this->_city;
        }

        $city = ORM::factory('City')
                ->where("seo_name", "=", $subdomain)
                ->cached(Date::WEEK)
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
        } else if ( $this->_subdomain  AND $city = $this->get_city_by_subdomain($this->_subdomain)) {
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

    public function get_last_city_id() {
        return $this->_last_city_id;
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

     public static function get_domain_by_city_old($domain_str, $url_str, $protocol_str = "http://")
    {
        $config = Kohana::$config->load("common");
        $main_domain = $config["main_domain"];
        $city = $domain_str;
        if (!$url_str){ $city = "";}
        if ($domain_str) {
            $domain_str .= ".";
        }
        if (!$url_str) {
            return sprintf('%s%s%s%s',  $protocol_str , $domain_str, $main_domain, ($city)? "/".$city:"" );
        } else {
            return sprintf('%s%s%s%s%s', $protocol_str, $domain_str, $main_domain, ($city)? "/".$city:"", self::url($url_str));
        }
    }
    
    public static function url($link)
    {
        return "/".$link;
    }
}