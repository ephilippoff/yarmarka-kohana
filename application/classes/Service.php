<?php defined('SYSPATH') or die('No direct script access.');

class Service
{
    protected $_city = NULL;
    protected $_category = NULL;
    protected $_quantity = NULL;
    protected $_object = NULL;
    protected $_price_config = NULL;

    public static function factory($service, $object_id = NULL)
    {   
        // Set class name
        $service = 'Service_'.$service;

        return new $service($object_id);
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

    public function getCityPrice($price_config, $city)
    {
        if (array_key_exists($city, $price_config)) {
            return $price_config[$city];
        }

        return $price_config;
    }

    public function getCategoryPrice($price_config, $category)
    {
        if (array_key_exists($category, $price_config)) {
            $price_config = $price_config[$category];
        } else {
            $category = ORM::factory('Category')->where("seo_name","=",$category)->find();
            if ($category->parent_id) {
                $parent_categories = ORM::factory('Category')->get_parent($category->parent_id);
                $parent_categories = array_map(function($item){
                    return $item->seo_name;
                }, $parent_categories);
                foreach ($parent_categories as $category) {
                    if (array_key_exists($category, $price_config)) {
                        return $price_config[$category];
                        break;
                    }
                }
            }
        }

        return $price_config;
    }

    public function getPrice($price_base = 1)
    {
        $price = 0;
        $price_config = $this->_price_config;
        if ( $city = $this->city() ) {
            if (!is_array($city)) {
                $city = array($city);
            }
            foreach ($city as $cityItem) {
                $city_price_config = $this->getCityPrice($price_config, $cityItem);
                if ( $category = $this->category() ) {
                    if (!is_array($category)) {
                        $category = array($category);
                    }
                    foreach ($category as $categoryItem) {
                        $category_price_config = $this->getCategoryPrice($city_price_config, $categoryItem);
                        $price += (( is_array($category_price_config) ) ? $category_price_config["default"] : $category_price_config ) * $price_base;
                    }
                } else {
                    $price += (( is_array($city_price_config) ) ? $city_price_config["default"] : $city_price_config) * $price_base;
                }
            }
        } else if ( $category = $this->category() ) {
            
            if (!is_array($category)) {
                $category = array($category);
            }

            $price_config = $this->getCategoryPrice($price_config, $category);
            $price += (( is_array($price_config) ) ? $price_config["default"] : $price_config) * $price_base;
        } else {
            $price += (( is_array($price_config) ) ? $price_config["default"] : $price_config) * $price_base;
        }
        return ($price) ? $price : $this->_price_base;
    }

    public function getPriceMultiple()
    {
        return $this->getPrice($this->_price_base);
    }

    public function city($city = NULL)
    {
        if (!$city) return $this->_city;
        if (!$this->_city) {
            $this->_city = array();
        }

        if (is_array($city)) {
            $this->_city = array_merge($this->_city, $city);
        } else {
            $this->_city[] = $city;
        }
        
        return $this;
    }

    public function category($category = NULL)
    {
        if (!$category) return $this->_category;
         if (!$this->_category) {
            $this->_category = array();
        }
         if (is_array($category)) {
            $this->_category = array_merge($this->_category, $category);
        } else {
            $this->_category[] = $category;
        }
        return $this;
    }

    public function quantity($quantity = NULL)
    {
        if (!$quantity) return $this->_quantity;
        $this->_quantity = $quantity;
        return $this;
    }

    public function object($object = NULL)
    {
        if (!$object) return $this->_object;
        $this->_object = $object;
        return $this;
    }

    public function set_defaults()
    {
        if (!$this->object()) return;
        $object = $this->object();
        $this->category( $object->category_obj->seo_name );
        $this->city( $object->city_obj->seo_name );
    }

    public function set_params($params = array())
    {
        $this->set_defaults();

        $params = new Obj($params);

        if ($params->category) {
            $this->category($params->category);
        }

        if ($params->city) {
            $this->city($params->city);
        }

        if ($params->quantity) {
            $this->quantity($params->quantity);
        }
    }

    public function get_title($params)
    {
        return "Услуга '".$params->service['title']."' для объявления '".$params->object['title']."'";
    }

    /**
     * [save This function save service to cart]
     * @param  [type] $service_info    [all about service]
     * @param  [type] $user_result     [all user parameters for service such as quantity, city, category etc]
     * @param  [type] $tempOrderItemId [ID of record in Order_ItemTemp table, set if is edit]
     * @return [void]
     */
    public function save($object_info, $key, $tempOrderItemId = NULL)
    {
        $params = new Obj();
        $params->service =  $this->get();
        $params->object =  $object_info;
        $params->title = $this->get_title($params);

        return $this->save_to_cart($params, $key, $tempOrderItemId);
    }

    protected function save_to_cart($params, $key, $tempOrderItemId = NULL)
    {
        $object_id = $params->object["id"];

        if ($tempOrderItemId) {
            $order_item_temp = ORM::factory('Order_ItemTemp', $tempOrderItemId);
            
        } else {
            $order_item_temp = ORM::factory('Order_ItemTemp')
                                        ->where("key","=",$key)
                                        ->where("object_id","=",$object_id)
                                        ->where("service_name","=", $params->service["name"])
                                        ->find();
        }

        if ($order_item_temp->loaded()) {
            $order_item_temp->return_reserve();
        }
        $order_item_temp->object_id = $object_id;
        $order_item_temp->service_id = NULL;
        $order_item_temp->service_name = $params->service["name"];
        $order_item_temp->params = json_encode( $params );
        $order_item_temp->key = $key;
        $order_item_temp->save();

        $order_item_temp->reserve($key);

        return $order_item_temp->get_row_as_obj();
    }

    public function apply($orderItem)
    {
        
    }

    public static function saveServiceInfoToCompiled($object_id)
    {
        $oc = ORM::factory('Object_Compiled')
                ->where("object_id","=",$object_id)
                ->find();
        $compiled = array();
        if ($oc->loaded()) {
            $compiled = unserialize($oc->compiled);
        }

        $compiled = array_merge($compiled, Object_Compile::getServices($object_id) );

        $oc->object_id = $object_id;
        $oc->compiled = serialize($compiled);
        $oc->save();
        
    }

    public function check_available($quantity)
    {
        return FALSE;
    }

}