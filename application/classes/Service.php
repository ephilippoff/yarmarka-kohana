<?php defined('SYSPATH') or die('No direct script access.');

class Service
{
    protected $_city = NULL;
    protected $_category = NULL;
    protected $_object_id = NULL;
    protected $_price_config = NULL;

    public static function factory($service, $param = NULL)
    {   
        // Set class name
        $service = 'Service_'.$service;

        return new $service($param);
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

    public function object($object_id)
    {
        $this->_object_id = $object_id;
        return $this;
    }

    public function reload_service_info($params)
    {
        if ($params->service["name"] == "object")
        {
            $service = Service::factory(Text::ucfirst($params->service["name"]), $params->object["id"]);
        } else {
            $service = Service::factory(Text::ucfirst($params->service["name"]));
        }
        if ($params->category) {
            $service = $service->category($params->category);
        }
        if ($params->city) {
            $service = $service->category($params->city);
        }

        return $service->get();
    }

    public function save($service_info)
    {

        $params = new Obj();
        $params->service = $this->reload_service_info($service_info);
        $params->quantity = ($params->quantity) ? $params->quantity : 1;
        $params->balance = ($params->balance) ? $params->balance : -1;
        $params->price =$params->service["price"];
        $params->total = $this->calculate_total((array) $params);
        $params->type = $service_info->service["name"];

        if ($params->service["name"] == "object")
        {
            $params->title = "<a href='/detail/".$service_info->object['id']."'>".$service_info->object['title']."</a>";
        } else
        {
            $params->title = "Услуга '".$params->service['title']."' для объявления <a href='/detail/".$service_info->object['id']."'>'".$service_info->object['title']."'</a>";
        }

        $total_params = json_encode(array_merge( (array) $service_info, (array) $params) ) ;

        $order_item_temp = ORM::factory('Order_ItemTemp');
        $order_item_temp->object_id = $service_info->object["id"];
        $order_item_temp->service_id = NULL;
        $order_item_temp->params = $total_params;
        $order_item_temp->key = Cart::get_key();
        $order_item_temp->save();

        return $order_item_temp;
    }

    public function apply()
    {
        
    }
}