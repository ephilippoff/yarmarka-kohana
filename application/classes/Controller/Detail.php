<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Detail extends Controller_Template {
    
    public function before()
    {
        parent::before();

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
    }

    public function action_index() {
        
        $object_seo_name = $this->request->param("object_seo_name");
        $object_category_segment = trim($this->request->param("path"), "/");
        $object_seo_name_segments = explode("-", $object_seo_name);

        $object_id =  (int) end($object_seo_name_segments);

        if ($object_id == 0) {
            throw new HTTP_Exception_404;
        }

        $object = ORM::factory('Objectcompiled', $object_id);
        if (!$object->loaded()) {
            throw new HTTP_Exception_404;
        }

        $url = $object->get_full_url();

        if ($url <> $this->request->get_full_url()) {
            HTTP::redirect($url);
        }

        $twig = Twig::factory('detail/index');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        $twig->crumbs      = Search_Url::get_category_crubms($object->category);
        $twig->object      = $object->get_row_as_obj();
        $twig->object_info = $object->get_compiled();
        
        $this->response->body($twig);

    }
} // End Detail
