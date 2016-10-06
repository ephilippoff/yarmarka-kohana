<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Service_Email extends ORM
{
    protected $_table_name = 'object_service_email';

    protected $_belongs_to = array(
        'object'    => array('model' => 'Object', 'foreign_key' => 'object_id'),
    );


    public function apply_service($object_id, $quantity = 1)
    {
        $or = ORM::factory('Object_Service_Email')
                    ->where("object_id", "=", $object_id)
                    ->find();
                    
        if ($or->loaded())
        {
            $or->date_expiration = DB::expr("date_expiration + interval '$quantity days'");
        } else {
            $or->date_expiration = DB::expr("NOW() + interval '$quantity days'");
        }
        
        
        $or->object_id = $object_id;
        $or->save();
    }


    public function get_actual($city_id)
    {
        return $this->select('object.id')
                    ->join('object')
                        ->on('object.id','=','object_service_email.object_id')
                    ->where("object_service_email.date_expiration", ">", DB::expr("NOW()"))
                    ->where("object.city_id", "=", $city_id)
                    ->find_and_map(function($item){
                        return $item->object_id;
                     });
    }

}