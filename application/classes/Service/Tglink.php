<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Tglink extends Service
{
   

    protected $_name = "tglink";
    protected $_title = "Тексто - графическая ссылка";
    protected $_is_multiple = TRUE;
    protected $_image;
    protected $_text;

    public function __construct($object_id = NULL)
    {
        $object = ORM::factory('Object',$object_id);
        if ($object->loaded()) {
            $this->object($object);
        }
        $this->_initialize();
    }

    public function get()
    {
        $quantity = ($this->quantity()) ? $this->quantity() : 1;
        $price = $price_total = $this->getPriceMultiple();
        

        $discount = 0;
        $discount_reason = "";
        $discount_name = FALSE;
        $price_total = $price * $quantity - $discount;
        $description = $this->get_params_description().$discount_reason;

        return array(
            "image" => $this->image(),
            "text" => $this->text(),
            "city" => $this->city(),
            "category" => $this->category(),
            "name" => $this->_name,
            "title" => $this->_title,
            "price" => $price,
            "quantity" => $quantity,
            "discount" => $discount,
            "discount_name" => $discount_name,
            "discount_reason" => $discount_reason,
            "price_total" => $price_total,
            "description" => $description
        );
    }

    public function getCategoryPrice($price_config, $category)
    {
        if (!is_array($price_config))  return $price_config;

        if (array_key_exists($category, $price_config)) {
            $price_config = $price_config[$category];
        }

        return $price_config;
    }

    public function set_params($params = array())
    {
        parent::set_params($params);

        $params = new Obj($params);

        if ($params->image) {
            $this->image($params->image);
        }

        if ($params->text) {
            $this->text($params->text);
        }
        
        return $this;
    }

    public function category($category = NULL)
    {
        if (!$category) return $this->_category;
        
        if (!$this->_category) {
            $this->_category = array();
        }

        $this->_category = $category;

        return $this;
    }

    public function image($image = NULL)
    {
        if (!$image) return $this->_image;
        
        if (!$this->_image) {
            $this->_image = array();
        }

        $this->_image = $image;

        return $this;
    }

     public function text($text = NULL)
    {
        if (!$text) return $this->_text;
        
        if (!$this->_text) {
            $this->_text = array();
        }

        $this->_text = $text;

        return $this;
    }

    public function get_params_description($params = array())
    {
        $categories = array(
            'tg1' => 1,
            'tg2' => 2,
            'tg3' => 3,
            'tg4' => 4,
            'tgmain' => 'На главной'
        );

        return "Количество: ".(($this->quantity()) ? $this->quantity() : 1)
                    . " (нед)<br>Блок рубрик: ". $categories[$this->category()]
                         . "<br>Текст: ". $this->text()
                            . "<br>Фон:<br>". sprintf('<img src="/static/develop/images/tglink/%s.jpg"', $this->image());
    }


    public function apply($orderItem)
    {
        
        $object_id = $orderItem->object->id;


        Service_Tglink::apply_service($object_id, $orderItem);
        //self::saveServiceInfoToCompiled($object_id);

        ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги %s: № %s", array( $this->_title, $orderItem->order_id ) ) );

    }

    static function apply_service($object_id, $orderItem)
    {
        $object = ORM::factory('Object', $object_id);

        if (!$object->loaded()) return FALSE;

        ORM::factory('Reklama')->apply_service($object, $orderItem);

        return TRUE;
    }
}