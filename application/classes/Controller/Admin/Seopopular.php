<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Seopopular extends Controller_Admin_Template {

    protected $module_name = 'seopopular';

    public function action_index()
    {
        $limit  = Arr::get($_GET, 'limit', 50);
        $page   = $this->request->query('page');
        $offset = ($page AND $page != 1) ? ($page-1) * $limit : 0;  
        
        $list = ORM::factory('Seo_Popular')->with_city();
        
        // количество общее
        $clone_to_count = clone $list;
        $count_all = $clone_to_count->count_all();      

        $list->limit($limit)->offset($offset);      
        
        $this->template->list = $list->order_by('id', 'desc')->find_all();
        
        $this->template->limit    = $limit;
        $this->template->pagination = Pagination::factory(array(
                'current_page'   => array('source' => 'query_string', 'key' => 'page'),
                'total_items'    => $count_all,
                'items_per_page' => $limit,
                'auto_hide'      => TRUE,
                'view'           => 'pagination/bootstrap',
            ))->route_params(array(
                'controller' => 'seopatterns',
                'action'     => 'index',
            ));                         
    }
    
    public function action_add()
    {
        $this->template->errors = array();  
        
        if (HTTP_Request::POST === $this->request->method()) 
        {
            try 
            {               
                $post = $_POST;

                if ( !isset($post['cities']) OR !isset($post['query']) ) throw new Exception("No found");
                
                foreach ($post['cities'] as $key => $value) {
                    ORM::factory('Seo_Popular')
                    ->where('city_id','=',$value)
                    ->where('query','=',$post["query"])
                    ->find()
                    ->values(array(
                        "city_id" => $value,
                        "query" => $post["query"],
                        "count" => $post['counts'][$key],
                    ))->save();
                }

                $this->redirect('khbackend/seopopular/index');
            } 
            catch (ORM_Validation_Exception $e) 
            {
                $this->template->errors = $e->errors('validation');
            }
        }

        $this->template->cities = ORM::factory('City')
                ->where('is_visible', '=', 1)
                ->order_by('sort_order')
                ->find_all();       
        
    }
    
    public function action_delete()
    {
        $this->auto_render = FALSE;

        $item = ORM::factory('Seo_Popular', $this->request->param('id'))->delete();
                    
        $this->redirect('khbackend/seopopular/index');

    }

    public function action_query()
    {
        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        
        $query = $this->request->query('query');
        $json = array();

        $sphinx                 = new Sphinx();
        
        $finded_by_city = $sphinx->searchGroupByCity($query)['cities'];

        function cmp($a, $b)
        {
            return strcmp($a->count, $b->count);
        }
        
        usort($finded_by_city, "cmp");

        $finded_by_city = array_reverse( $finded_by_city );

        $json['limit'] = 5;
        $json['cities'] = $finded_by_city;
        $json['code'] = 200;

        $this->response->body(json_encode($json));
    }
        
}
