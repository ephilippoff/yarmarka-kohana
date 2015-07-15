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
            return;
        }

        $is_old = $this->request->param("is_old");
        if ($is_old) {
            HTTP::redirect("detail/".$this->request->param("object_id").".html", 301);
            return;
        }

        //TODO set header Last-Modified
        //$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', mysql_to_unix($last_modified)).' GMT');
    }

    public function action_index() {
        
        $object = $this->request->param("object");
        $url = $this->request->param("url");

        if ($url <> $this->request->get_full_url()) {
            HTTP::redirect($url, 301);
        }

        $twig = Twig::factory('detail/index');
        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();

        //блок информации которую можно закеширвоать всю разом
        $detail_info = $this->get_detail_info($object, array(
            "user", "crumbs", "object", "object_compiled", "author",
            "attributes", "images"
        ));

        foreach ((array) $detail_info as $key => $item) {
            $twig->{$key} = $item;
        }

        //блоки взаимодействия которые кешируются отдельно
        $detail_interact = $this->get_detail_interact($object, array(
            "messages", "search_cache", "similar"
        ));

        foreach ((array) $detail_interact as $key => $item) {
            $twig->{$key} = $item;
        }

        $this->response->body($twig);
    }

    public function action_type89() {

    }

    public function action_type88() {

    }

    public function action_type90() {

    }

    public function get_detail_info(ORM $object, $need = array()) {
        $result = new Obj();

        if (in_array("user", $need)) {
            $result->user        = Auth::instance()->get_user();
        }

        if (in_array("object", $need) AND $object->loaded()) {
            $result->object      = $object->get_row_as_obj();
        }

        if (in_array("object_compiled", $need) AND $result->object) {
            $result->object_compiled = new Obj($object->get_compiled());
        }

        if (in_array("crumbs", $need) AND $result->object) {
            $result->crumbs      = Search_Url::get_category_crubms($object->category);
        }

        if (in_array("author", $need) AND $result->object) {
            $author_id = $result->object->author;
            if ($result->object->org_type == 1)
                $author_id = $result->object->author;
            elseif ($result->object->author_company_id <> $result->object->author and $result->object->org_type == 2)
                $author_id = $result->object->author_company_id;

            $result->author      = ORM::factory('User')
                                    ->where("id","=",$author_id)
                                    ->find()->get_row_as_obj();

        }

        if (in_array("attributes", $need) AND $result->object_compiled) {
            $result->attributes  = Object_Compiled::getAttributes($result->object_compiled->attributes);
        }

        if (in_array("images", $need) AND $result->object_compiled) {
            $result->images      = Object_Compiled::getImages($result->object_compiled->photo);
        }

        return $result;
    }

    public function get_detail_interact(ORM $object, $need = array()) {
        $result = new Obj();

        if (in_array("messages", $need) AND $object->loaded()) {
            $result->messages = ORM::factory('User_Messages')
                                ->get_messages($object->id)
                                ->getprepared_all();
        }

        if (in_array("search_cache", $need)) {
            $result->search_cache = Search::get_search_cache();
        }

        if (in_array("similar", $need)) {
            try {
                $result->similar = Search::get_similar_objects_by_cache($result->search_cache);
            } catch (Exception $e) {}
        }

        return $result;
    }

    public function after()
    {
        parent::after();
        $object = $this->request->param("object");
        Cookie::save_toobject_history($object->id);
    }
} // End Detail
