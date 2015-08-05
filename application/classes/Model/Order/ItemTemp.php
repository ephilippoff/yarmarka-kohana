<?php

class Model_Order_ItemTemp extends ORM
{
    protected $_table_name = 'order_item_temp';

    function get_info() {
        if (!$this->loaded()) return false;

        if ($this->object_id) {
            return $this->get_object_info();
        } elseif ($this->service_id){
            return true;
        }

        return false;
    }

    function get_object_info()  {

        $result = array();
        $balance = -1;
        $object = ORM::factory('Object', $this->object_id);

        if ($object->loaded()) {
            $result["object_id"] = $object->id;
            $result["title"] = $object->title;
            $result["balance"] = ORM::factory('Object')->get_balance($this->object_id);
            $result["quantity"] = 1;
            $result["price"] = intval($object->price * $result["quantity"]);
        }
        return $result;
    }

    

    function save_object_to_order_item_temp($object_id, $key, $params = array()) {

        $this->db->insert('order_item_temp', array(
            'object_id' => $object_id,
            'key' => $key,
            'params' => json_encode($params)
        ));

        return $this->db->insert_id();
    }

    // function get_balance($object_id) {

 //        $this->db->select('data_integer.value_min as value_min');
 //        $this->db->from('data_integer');
 //        $this->db->join('attribute', 'data_integer.attribute = attribute.id', 'left');
 //        $this->db->where('attribute.seo_name', 'balance');
 //        $this->db->where('data_integer.object', $object_id);
 //        $query = $this->db->get();

 //        $balance = -1;
 //        $row = $query->row();
 //        if ($row) {
 //             $balance = $row->value_min;
 //        } 
 //        return $balance;
 //    }

    // function delete_object_from_order_item_temp($object_id, $key) {
    //     return $this->db->delete('order_item_temp', array('object_id' => intval($object_id), 'key' => $key));
    // }

    // function get_cart_count($key)  {
    //     $this->db->select('count(id) as count');
                            
    //     $this->db->from('order_item_temp');
        
    //     $this->db->where('key', $key);

    //     $query = $this->db->get();
        
    //     return $query->row()->count;
    // }
}