<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search_Landing extends Controller_Search {

	public function action_avtotransport() {

        $start = microtime(true);

        $twig = Twig::factory('landing/avtotransport');

        $search_info = $this->get_search_info();

        //main search
        $main_search_query = Search::searchquery($search_info->search_filters, $search_info->search_params);
        
        $twig->main_search_result = Search::getresult($main_search_query->execute()->as_array());


        if (!$search_info->main_search_result_count) {
            $main_search_result_count = Search::searchquery($search_info->search_filters, array(), array("count" => TRUE))
                                                    ->execute()
                                                    ->get("count");
            $search_info->main_search_result_count = $main_search_result_count;
        }
        //end main search

        //premium
        $premium_search_query = Search::searchquery(
            array_merge($search_info->search_filters, array("premium" => TRUE)), 
            array_merge($search_info->search_params, array("limit" => 5))
        );
        $twig->premium_search_result = Search::getresult($premium_search_query->execute()->as_array());
        //premium end
        
        //vip
        $vip_search_query = Search::searchquery(
            array(
                "photocard" => TRUE,
                "active" => TRUE,
                "published" =>TRUE,
                "city_id" => $search_info->city_id,
                "category_id" => (count($search_info->child_categories_ids) > 0) ? $search_info->child_categories_ids : $search_info->category->id,
            ),
            array_merge($search_info->search_params, array("limit" => 15))
        );
        $twig->vip_search_result = Search::getresult($vip_search_query->execute()->as_array());
        //vip end

        //pagination
        $pagination = Pagination::factory( array(
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'total_items' => $search_info->main_search_result_count,
            'items_per_page' => $search_info->search_params['limit'],
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

        $twig->small_pagination = (array(
            "prev" => $pagination->previous_page,
            "prev_url" => $pagination->url($pagination->previous_page),
            "next" => $pagination->next_page,
            "next_url" => $pagination->url($pagination->next_page),
            "current" => $pagination->current_page,
            "total" => $pagination->total_pages,
        ));
        $twig->pagination = $pagination;
        //pagination end

        if (!$this->cached_search_info) {
            $cache = $this->save_search_info_to_cache(array(
                    "info" => $search_info,
                    "canonical_url" =>  $search_info->canonical_url,
                    "sql" => (string) $main_search_query,
                    "count" => $search_info->main_search_result_count,
                )
            );
            Cookie::set('search_hash', $cache->hash, strtotime( '+14 days' ));
        }

        foreach ((array) $search_info as $key => $item) {
            $twig->{$key} = $item;
        }

        $twig->php_time = microtime(true) - $start;
        $this->response->body($twig);
    }
}
