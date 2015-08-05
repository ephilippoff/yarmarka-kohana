<?php defined('SYSPATH') or die('No direct script access.');

class Service
{
    protected $_city = NULL;
    protected $_category = NULL;
    protected $_price_config = NULL;

    public static function factory($service)
    {   
        // Set class name
        $service = 'Service_'.$service;

        return new $service();
    }

    public function _initialize()
    {
        $this->_price_config = Kohana::$config->load("services.".$this->_name);
        $this->_price_base = Kohana::$config->load("services.".$this->_name.".base");
        $this->_price_base = ($this->_price_base) ? $this->_price_base : 0;
    }

    public function calculate_total($options = array())
    {
        return $this->getPrice();
    }

    public function getPrice()
    {
        return ( is_array($this->_price_config) ) ? $this->_price_config["default"] : $this->_price_config;
    }

    public function getPriceMultiple()
    {
        return (( is_array($this->_price_config) ) ? $this->_price_config["default"] : $this->_price_config) * $this->_price_base;
    }

    public function city($city = "")
    {
        if (array_key_exists($city, $this->_price_config)) {
            $this->_price_config = $this->_price_config[$city];
        }

        $this->_city = $city;
        return $this;
    }

    public function category($category = "")
    {
        if (array_key_exists($category, $this->_price_config)) {
            $this->_price_config = $this->_price_config[$category];
        } else {
            $category = ORM::factory('Category')->where("seo_name","=",$category)->find();
            if ($category->parent_id) {
                $parent_categories = ORM::factory('Category')->get_parent($category->parent_id);
                $parent_categories = array_map(function($item){
                    return $item->seo_name;
                }, $parent_categories);
                foreach ($parent_categories as $category) {
                    if (array_key_exists($category, $this->_price_config)) {
                        $this->_price_config = $this->_price_config[$category];
                        break;
                    }
                }
            }
        }

        $this->_category = $category;
        return $this;
    }


}