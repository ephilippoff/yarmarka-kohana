<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User_Search extends Controller_Template {

    var $user; // current user
    private $errors = array();

    public function before()
    {
        parent::before();
        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        $this->domain = new Domain();
        $this->twig = Twig::factory('search/user/index');

        $this->user = Auth::instance()->get_user();
        
        if (!$this->user)
        {
            $this->redirect(Url::site('user/login?return='.$this->request->uri()));
        }

        $uri = $this->request->uri();
        $route_params = $this->request->param();
        $query_params = $this->request->query();
        $this->twig->main_category = $this->domain->get_main_category();
        $category_path = ( isset($route_params['category_path']) ) ? $route_params['category_path'] : $this->twig->main_category;
        $this->twig->category_url = $category_path;
        $searchuri = new Search_Url($category_path, $query_params);
        $this->params_by_uri = $searchuri;

        $this->category_id = $this->params_by_uri->get_category()->id;
        $this->child_categories_ids = $this->params_by_uri->get_category_childs_id();
        $this->twig->crumbs = $this->params_by_uri->get_category_crubms($this->category_id);

        //favourites
        $this->twig->favourites = ORM::factory('Favourite')->get_list_by_cookie();
        //end favourites
        
        //messages
        $this->twig->messages = ORM::factory('User_Messages')
                                ->get_messages_user_objects($this->user->id)
                                ->order_by("createdOn", "desc")
                                ->limit(20)
                                ->getprepared_all();
        //end message
    }

    public function get_user_search_page()
    {

        $search_params = array(
            "page" => $this->params_by_uri->get_reserved_query_params("page"),
            "limit" => $this->params_by_uri->get_reserved_query_params("limit")
        );

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
            'limits' => array(
                "30" => $this->url_with_query(array(), array("page","limit")),
                "60" => $this->url_with_query(array( "limit" => 60), array("page")),
                "90" => $this->url_with_query(array( "limit" => 90), array("page")),
            )
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
        $user_categories = Search::searchquery($this->search_filters, array(), array("group_category" => TRUE))
                                                    ->execute()->as_array();

        $user_categories  = array_merge(
            array(array("title"=>"Все", "url" => "")),
            $user_categories
        );
        $this->twig->user_categories = $user_categories;

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

        $this->twig->seo_attributes = new Obj(array(
            "title" => "Мои опубликованные объявления - Личный кабинет",
            "h1" => "Мои опубликованные объявления"
        ));

        $this->get_user_search_page();
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
            "title" => "Мои снятые и архивные объявления - Личный кабинет",
            "h1" => "Мои снятые и архивные объявления"
        ));

        $this->get_user_search_page();
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
            "title" => "Избранные объявления - Личный кабинет",
            "h1" => "Избранные объявления"
        ));

        $this->get_user_search_page();
    }

    public function after()
    {
        parent::after();
        $this->response->body($this->twig);
    }

    public function url_with_query($params = array(), $unset_params = array()) {
        $query_params = $this->request->query();
        foreach ($params as $key => $value) {
            $query_params[$key] = $value;
        }
        foreach ($unset_params as $unset_param) {
            unset($query_params[$unset_param]);
        }

        $query_str = http_build_query($query_params);
        return $this->request->route()->uri($this->request->param()).($query_str?"?".$query_str:"");
    }
}
