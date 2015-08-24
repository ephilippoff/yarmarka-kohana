<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User_Search extends Controller_Template {

    var $user; // current user
    private $errors = array();

    public function before()
    {
        parent::before();

        $this->performance = Performance::factory(Acl::check('profiler'));

        $this->performance->add("UserSearch","start");

        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
        $this->twig = Twig::factory('search/user/index');
        $this->twig->city = $this->domain->get_city();
        $this->user = Auth::instance()->get_user();
        $this->is_owner = TRUE;

        if ( $this->request->query("id") ) {
            $user = ORM::factory('User', $this->request->query("id") );
            if ($user->loaded() AND ($user->org_type == 2 AND $this->request->action() == "published") OR Acl::check('object.moderate') ) {
                $this->user = $user;
                $this->is_owner = FALSE;
                $this->twig->set_filename('search/user/company/index');
            } else {
                throw new HTTP_Exception_404;
                return;
            }
        }

        if (!$this->user and $this->request->action() <> "favorites")
        {
            $this->redirect(URL::site('user/login?return='.$this->request->uri()));
        }

        $this->twig->user = $this->user;
        $uri = $this->request->uri();
        $route_params = $this->request->param();
        $query_params = $this->request->query();
       
        $this->twig->query_url_str = Search_Url::get_suri_without_reserved($query_params);
        $this->twig->main_category = $this->domain->get_main_category();
        
        $category_path = ( isset($route_params['category_path']) ) ? $route_params['category_path'] : $this->twig->main_category;
        $this->twig->category_url = $category_path;
        
        $searchuri = new Search_Url($category_path, $query_params);
        $this->params_by_uri = $searchuri;

        $this->category = $this->params_by_uri->get_category();
        $this->category_id = $this->category->id;
        $this->child_categories_ids = $this->params_by_uri->get_category_childs_id();
        $this->twig->crumbs = $this->params_by_uri->get_category_crubms($this->category_id);

        $this->performance->add("UserSearch","favourite");
        //favourites
        $this->twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
        //end favourites
        
        $this->performance->add("UserSearch","messages");
        //messages
        if ($this->user)
        {
            $this->twig->messages = ORM::factory('User_Messages')
                                    ->get_messages_user_objects($this->user->id)
                                    ->order_by("createdOn", "desc")
                                    ->limit(20)
                                    ->getprepared_all();
        }
        //end message
        
        $this->twig->s_host = URL::SERVER("HTTP_HOST");
        $this->twig->s_suri = URL::SERVER("REQUEST_URI");
    }

    public function get_user_search_page($action)
    {

        $search_params = array(
            "page" => $this->params_by_uri->get_reserved_query_params("page"),
            "limit" => $this->params_by_uri->get_reserved_query_params("limit")
        );
        $this->performance->add("UserSearch","main_search_query");
        $main_search_query = Search::searchquery($this->search_filters, $search_params);
        $this->twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());
        
        $main_search_result_count = Search::searchquery($this->search_filters, array(), array("count" => TRUE))
                                                    ->execute()
                                                    ->get("count");
        $this->twig->main_search_result_count = $main_search_result_count;

        //pagination
        $pagination = Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $main_search_result_count,
            'items_per_page' => $search_params['limit'],
            'auto_hide' => TRUE,
            'view' => 'pagination/search',
            'first_page_in_url' => FALSE,
            'count_out' => 1,
            'count_in' => 8,
            'path' => URL::SERVER("PATH_INFO"),
            'limits' => array(
                "30" => Search_Url::get_suri_without_reserved($this->request->query(),array(),array("limit","page")),
                "60" => Search_Url::get_suri_without_reserved($this->request->query(), array( "limit" => 60), array("page")),
                "90" => Search_Url::get_suri_without_reserved($this->request->query(), array( "limit" => 90), array("page")),
            )
        ))->route_params(array(
            'controller' => 'User_Search',
            'action'     => $action,
        ));

        $small_pagination = (array(
            "prev" => $pagination->previous_page,
            "prev_url" => $pagination->url($pagination->previous_page),
            "next" => $pagination->next_page,
            "next_url" => $pagination->url($pagination->next_page),
            "current" => $pagination->current_page,
            "total" => $pagination->total_pages,
        ));

        $this->twig->pagination = $pagination;
        $this->twig->small_pagination = $small_pagination;
        //end pagination

        $this->search_filters["category_id"] = NULL;
        $this->performance->add("UserSearch","user_categories_query");
        $user_categories = Search::searchquery($this->search_filters, array(), array("group_category" => TRUE))
                                                    ->execute()->as_array();

        $user_categories  = array_merge(
            array(array("title"=>"Все", "url" => "")),
            $user_categories
        );

        $this->twig->user_categories = $user_categories;

        //get balance for premium ads               
        $this->performance->add("UserSearch","premium_balance");
        $premium_balance = (int) Service_Premium::get_balance($this->user);
        $this->twig->premium_balance = $premium_balance;
    }

    public function action_published()
    {
        $this->twig->canonical_url = "user/published";
        $this->twig->empty_text = "У Вас ни одного опубликованного объявления в выбранной рубрике";
        $this->search_filters = array(
            "active" => TRUE,
            "published" =>TRUE,
            "user_id" => $this->user->id,
            "category_id" =>  (count($this->child_categories_ids) > 0) ? $this->child_categories_ids : $this->category_id,
            "filters" => array()
        );


        if ($this->is_owner)
        {
            $this->twig->seo_attributes = new Obj(array(
                "title" => "Личный кабинет - Мои опубликованные объявления",
                "h1" => "Личный кабинет - Мои опубликованные объявления"
            ));
        } else {
            $org_name = $this->user->org_name;
            $category_title = ($this->category_id > 1) ? " в рубрике ".$this->category->title : "";
            $this->twig->seo_attributes = new Obj(array(
                "title" => "Все объявления компании '".$org_name."' ".$category_title,
                "h1" => "Все объявления компании '".$org_name."' ".$category_title,
            ));
        }

        $this->get_user_search_page("");
    }

    public function action_unpublished()
    {
        $this->twig->canonical_url = "user/unpublished";
        $this->twig->empty_text = "У Вас ни одного архивного объявления в выбранной рубрике";
        $this->search_filters = array(
            "active" => TRUE,
            "published" => FALSE,
            "user_id" => $this->user->id,
            "category_id" =>  (count($this->child_categories_ids) > 0) ? $this->child_categories_ids : $this->category_id,
            "filters" => array()
        );

        $this->twig->seo_attributes = new Obj(array(
            "title" => "Личный кабинет - Мои снятые и архивные объявления",
            "h1" => "Личный кабинет - Мои снятые и архивные объявления"
        ));

        $this->get_user_search_page("unpublished");
    }

    public function action_favorites()
    {
        $this->twig->canonical_url = "user/favorites";
        $this->search_filters = array(
            "active" => TRUE,
            "is_favorite" =>TRUE,
            "category_id" =>  (count($this->child_categories_ids) > 0) ? $this->child_categories_ids : $this->category_id,
            "filters" => array()
        );

        $this->twig->seo_attributes = new Obj(array(
            "title" => "Личный кабинет - Избранные объявления",
            "h1" => "Личный кабинет - Избранные объявления"
        ));

        $this->get_user_search_page("favorites");
    }

    public function after()
    {
        parent::after();
        $this->performance->add("UserSearch","end");
        $this->twig->php_time = $this->performance->getProfilerStat();
        $this->response->body($this->twig);

    }
}
