<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Detail extends Controller_Template {
    public function before()
    {
        parent::before();
    }

    public function action_index() {
        
        $object_seo_name = $this->request->param("object_seo_name");
        $object_category_segment = trim($this->request->param("path"), "/");
        $object_seo_name_segments = explode("-", $object_seo_name);

        $object_id =  (int) end($object_seo_name_segments);

        if ($object_id == 0) {
            throw new HTTP_Exception_404;
        }

        $object = ORM::factory('Object', $object_id);
        if (!$object->loaded()) {
            throw new HTTP_Exception_404;
        }

        $proper_object_category_segment = $object->get_category_segment();
        if ($proper_object_category_segment <> $object_category_segment 
                OR $object_seo_name <> $object->seo_name."-".$object->id) {
            HTTP::redirect($object->get_url($proper_object_category_segment));
        }
        
    }
} // End Detail
