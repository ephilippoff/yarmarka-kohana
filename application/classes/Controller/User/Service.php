<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User_Service extends Controller_User_Profile {

    public function before()
    {
        parent::before();
        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
    }

    public function action_massload_conformities()
    {   
        $twig = Twig::factory('user/massload_conformities');
        $twig->city = $this->domain->get_city();

        $twig->user = $this->user;
          
        $twig->block_name = "user/_massload_conformities";
        $twig->params = new Obj();

        $categories = Array();
        $category = $this->request->param('category');
        if(!$category)
            throw new HTTP_Exception_404;

        $user = $this->user;
        $user_id = $this->request->param('user_id');
        $twig->params->end_user_id = $user_id;
        
        if ($this->user->role == 1 AND $user_id)
            $user = ORM::factory('User', $user_id);

        try 
        { 
            $cfg = Kohana::$config->load('massload/bycategory.'.$category);
            $categories[$category] = $cfg["name"];
        } catch(Exception $e){
            throw new HTTP_Exception_404;
        }
        $twig->params->categories = $categories;

        $conformities = Array();
        $dictionaries = Array();
        $forms = Array();
        foreach ($categories as $key=>$value)
        {
            $cfg = Kohana::$config->load('massload/bycategory.'.$key);
            @list($dictionary, $form) = Massload::get_dictionary($cfg, $user->id, $key);
            $dictionaries[$key] = $dictionary;
            $forms[$key] = $form; 
        }
        $twig->params->dictionaries   = $dictionaries;
        $twig->params->forms          = $forms;
        $twig->params->conformities   = $conformities;
        $twig->params->cfg            = $cfg;
        $twig->params->user            = $user;

        $twig->params = (array) $twig->params;
  
        $this->response->body($twig);
    }

    public function action_objectload()
    {
        $twig = Twig::factory('user/objectload');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/objectload";
        $twig->user = $this->user;
        $twig->block_name = "user/_objectload";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Массовая загрузка объявлений"),
        );

        $user = $this->user;

        $already_agree = FALSE;
        $us = ORM::factory('User_Settings')
                        ->get_by_name($this->user->id, "massload_agreed")
                        ->find();
        $already_agree = $us->loaded();

        $hidehelp = FALSE;
        $us = ORM::factory('User_Settings')
                        ->get_by_name($this->user->id, "massload_hidehelp")
                        ->find();
        $hidehelp = !$us->loaded();

        if (HTTP_Request::POST === $this->request->method())
        {
            $post = new Obj($_POST);
            if ($post->i_agree AND !$already_agree)
            {
                $us->user_id = $this->user->id;
                $us->name    = "massload_agreed";
                $us->value   = 1;
                $us->save();                
            }

            if ($post->hidehelp AND $hidehelp)
            {
                $us->user_id = $this->user->id;
                $us->name    = "massload_hidehelp";
                $us->value   = 1;
                $us->save();                
            }

            if (!$post->hidehelp AND !$hidehelp)
                $us->delete();              

            header("Refresh:0");
            exit;
        }

        $twig->params->already_agree  = $already_agree;
        $twig->params->hidehelp       = $hidehelp;

        $avail_categories = Kohana::$config->load('massload.frontend_load_category');
        
        $categories = Array();                              
        $categories_templatelink = Array();
        foreach($avail_categories as $category)
        {
                $cfg = Kohana::$config->load('massload/bycategory.'.$category);
                $categories[$category] = $cfg["name"];

                $us = ORM::factory('User_Settings')
                        ->get_by_name(13, $category)
                        ->cached()
                        ->find();

                $categories_templatelink[$category] = ($us->value) ? $us->value : "#";
        }
        $twig->params->categories = $categories;
        $twig->params->categories_templates = $categories_templatelink;
        $twig->params->config = Kohana::$config->load('massload/bycategory');
        $twig->params->free_limit = Kohana::$config->load('massload.free_limit');

        $objectload         = ORM::factory('Objectload');
        $objectload_files   = ORM::factory('Objectload_Files');     

        $oloads = $objectload->where("user_id","=",$this->user->id)
                                ->order_by("created_on", "desc")
                                ->limit(5)
                                ->find_all();       

        $twig->params->objectloads = $objectload->get_objectload_list($oloads);
        $twig->params->states      = $objectload->get_states();
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_orders()
    {
        $twig = Twig::factory('user/orders');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/orders";
        $twig->user = $this->user;

        $twig->crumbs = array(
            array("title" => "Личный кабинет - История заказов"),
        );

        $user = $this->user;
        $page = (int) $this->request->query("page");
        $page = ($page) ? $page : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orders = ORM::factory('Order')
                ->where("user_id","=",$this->user->id)
                ->order_by("created", "desc")
                ->offset($offset)
                ->limit($limit)
                ->getprepared_all();

        if (count($orders) > 0 )
        {
            $order_items = ORM::factory('Order_Item')
                                ->where("order_id","IN", array_map(function($item){return $item->id;},  $orders))
                                ->getprepared_all();
                                

            foreach ($orders as $order) {
                $order->state_name = Model_Order::get_state($order->state);
                $order->items = array_filter($order_items, function($item) use ($order){ return ($order->id == $item->order_id);});
                foreach ($order->items as $order_item) {
                    $order_item->params = json_decode($order_item->params);
                }
            }
        }

       
        $twig->orders = $orders;

        $pagination = Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => ORM::factory('Order')->where("user_id","=", $this->user->id)->count_all(),
            'items_per_page' => $limit,
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'count_out' => 1,
            'count_in' => 8,
            'limits' => array()
        ))->route_params(array(
            'controller' => 'User',
            'action' => 'orders',
        ));;

        $twig->pagination = $pagination;

        $this->response->body($twig);
    }

    public function action_subscriptions()
    {

        $twig = Twig::factory('user/subscriptions');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/subscriptions";
        $twig->user = $this->user;

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Подписки"),
        );

        $user = $this->user;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'enable') {
                $model = ORM::factory('Subscription_Surgut', $_POST['id']);
                if ($model->loaded() && $model->check_is_mine()) {
                    $model->enabled = $model->get_not_enabled();
                    $model->save();
                }
            }

            if ($_POST['action'] == 'remove') {
                $model = ORM::factory('Subscription_Surgut', $_POST['id']);
                if ($model->loaded() && $model->check_is_mine()) {
                    $model->delete();
                }
            }

            $this->redirect('/user/subscriptions');
        }

        // pagination settings
        $per_page   = 20;
        $page       = (int) Arr::get($_GET, 'page', 1);

        $subscriptions = ORM::factory('Subscription_Surgut')
            ->where('user_id', '=', $user->id);

        $count = clone $subscriptions;
        $count = $count->count_all();

        $subscriptions->limit($per_page)
            ->offset($per_page*($page-1));

        $twig->subscriptions = $subscriptions->find_all();
        // $this->template->pagination = Pagination::factory( array(
        //     'current_page' => array('source' => 'query_string', 'key' => 'page'),
        //     'total_items' => $count,
        //     'items_per_page' => $per_page,
        //     'auto_hide' => TRUE,
        //     'view' => 'pagination/floating',
        //     'first_page_in_url' => TRUE,
        //     'count_out' => 5,
        //     'count_in' => 5
        // ))->route_params(array(
        //     'controller' => 'user',
        //     'action' => 'subscriptions',
        // ));
        $this->response->body($twig);
    }
}