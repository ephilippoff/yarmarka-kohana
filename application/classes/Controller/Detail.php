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
        $start = microtime(true);
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
            "crumbs", "object", "object_compiled", "author",
            "attributes", "images"
        ));

        foreach ((array) $detail_info as $key => $item) {
            $twig->{$key} = $item;
        }

        //блоки взаимодействия которые кешируются отдельно
        $detail_interact = $this->get_detail_interact($object, array(
            "user", "messages", "search_cache", "similar"
        ));

        foreach ((array) $detail_interact as $key => $item) {
            $twig->{$key} = $item;
        }

        $twig->php_time = microtime(true) - $start;
        $this->response->body($twig);
    }

    public function action_type89() {

    }

    public function action_type88() {

    }

    public function action_type90() {

    }

    public function get_detail_info(ORM $object, $need = array()) {
        $info = new Obj();

        

        if (in_array("object", $need) AND $object->loaded()) {
            $info->object      = $object->get_row_as_obj();
        }

        if (in_array("object_compiled", $need) AND $info->object) {
           $info->object->compiled =  Search::getresultrow((array) $info->object);
        }

        if (in_array("crumbs", $need) AND $info->object) {
            $info->crumbs      = Search_Url::get_category_crubms($object->category);
        }

        // if (in_array("author", $need) AND $info->object) {
        //     $author_id = $info->object->author;
        //     if ($info->object->org_type == 1)
        //         $author_id = $info->object->author;
        //     elseif ($info->object->author_company_id <> $info->object->author and $info->object->org_type == 2)
        //         $author_id = $info->object->author_company_id;

        //     $info->author      = ORM::factory('User')
        //                             ->where("id","=",$author_id)
        //                             ->find()->get_row_as_obj();

        // }
        return $info;
    }

    public function get_detail_interact(ORM $object, $need = array()) {
        $info = new Obj();

        if (in_array("user", $need)) {
            $info->user        = Auth::instance()->get_user();
            if ($info->user) {
                $info->user = $info->user->get_row_as_obj();
            }
        }

        if (in_array("messages", $need) AND $object->loaded()) {
            $info->messages = ORM::factory('User_Messages')
                                ->get_messages($object->id)
                                ->getprepared_all();
        }

        if (in_array("similar", $need)) {
            $similar_search_query = Search::searchquery(
                array(
                    "hash" => Cookie::get('search_hash'),
                    "not_id" => Cookie::get('ohistory') ? 
                                        array_merge(explode(",", Cookie::get('ohistory')), array($object->id)) 
                                            : array($object->id)
                ),
                array("limit" => 3, "page" => 0)
            );
            $info->similar_search_result = Search::getresult($similar_search_query->execute()->as_array());
        }

        return $info;
    }

    public function after()
    {
        parent::after();
        $object = $this->request->param("object");
        Cookie::save_toobject_history($object->id);
    }
} // End Detail
