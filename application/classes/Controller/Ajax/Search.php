<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Search extends Controller_Ajax {

    public function action_filters_submit(){

        $filters = json_decode($this->request->body());
        parse_str($filters->queryString, $query_params);

        $category_id = (int) $filters->category_id;
        $city_id = (int) $filters->city_id;

        $seo_segment_url = "";
        $cleaned_filters_without_seo = array();
        $cleaned_filters_all = array();
        $seo_segment_is_null = FALSE;

        $cleaned_query_params = Search_Url::clean_query_params($category_id, $query_params);
        foreach ($cleaned_query_params as $key => $item) {
            $value = $item["value"];
            if ($item["attribute"]->type == "list") {
                $value = array_values($value);
            }

            if ($item["attribute"]->type == "list" and $item["attribute"]->is_seo_used and count($value) > 1) {
                $seo_segment_is_null = TRUE;
            }

            if ($item["attribute"]->type == "list" and $item["attribute"]->is_seo_used and count($item["value"]) == 1) {
                $seo_segment_url = Search_Url::get_seo_param_segment(reset($value));
            } else {
                $cleaned_filters_without_seo[$key] = $value;
            }

            $cleaned_filters_all[$key] = $value;
        }

        $seo_segment_url = ($seo_segment_is_null) ? "" : $seo_segment_url;
        $cleaned_filters = ($seo_segment_is_null) ? $cleaned_filters_all : $cleaned_filters_without_seo;
        $cleaned_reserved_filters = Search_Url::get_query_params_without_reserved( Search_Url::clean_reserved_query_params($query_params));
        $cleaned_filters = array_merge($cleaned_filters, $cleaned_reserved_filters);

        $this->json["seo_segment_url"] = $seo_segment_url;
        $this->json["query"] = http_build_query($cleaned_filters);
    }

    public function action_filters_check(){
        $filters = json_decode($this->request->body());
        parse_str($filters->queryString, $query_params);

        $category_id = (int) $filters->category_id;
        $city_id = (int) $filters->city_id;

        $cleaned_filters_all = array();

        $cleaned_query_params = Search_Url::clean_query_params($category_id, $query_params);
        foreach ($cleaned_query_params as $key => $item) {
            $cleaned_filters_all[$key] = $item["value"];
        }

        $search_filters = array(
            "active" => TRUE,
            "published" =>TRUE,
            "city_id" => $city_id,
            "category_id" => $category_id,
            "filters" => $cleaned_filters_all
        );

        $count = Search::searchquery($search_filters, array(), array("count" => TRUE))
                                        ->execute()
                                        ->get("count");
        $this->json["count"] = $count;
    }
}