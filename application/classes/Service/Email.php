<?php defined('SYSPATH') OR die('No direct script access.');

class Service_Email extends Service
{
    const EM_DAYS = 1;

    protected $_name = "email";
    protected $_title = "E-mail маркетинг";
    protected $_is_multiple = TRUE;
    public $_period = 1;

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
        
        if ($quantity > 1) {
            $discount = 0;
            $discount_reason = "";
            $discount_name = FALSE;
        } else {
            $discount = $price * $quantity ;
            $discount_reason = " (бесплатно)";
            $discount_name = "free_email";
        }
       
        $price_total = $price * $quantity - $discount;
        $description = $this->get_params_description().$discount_reason;

        return array(
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

    public function get_params_description($params = array())
    {
        return "Количество: ".(($this->quantity()) ? $this->quantity() : 1)." (дней)";
    }


    public function apply($orderItem)
    {
        $quantity = $orderItem->service->quantity;
        $object_id = $orderItem->object->id;


        Service_Email::apply_service($object_id, $quantity);
        //self::saveServiceInfoToCompiled($object_id);

        ORM::factory('Order_Log')->write($orderItem->order_id, "notice", vsprintf("Активация услуги E-mail маркетинг: № %s", array( $orderItem->order_id ) ) );

    }

    static function apply_service($object_id, $quantity)
    {
        $object = ORM::factory('Object', $object_id);

        if (!$object->loaded()) return FALSE;


        ORM::factory('Object_Service_Email')->apply_service($object_id,  $quantity);

        return TRUE;
    }
}