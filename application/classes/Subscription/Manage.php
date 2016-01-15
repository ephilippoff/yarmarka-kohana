<?php

	class Subscription_Manage {

		public static function get_subscription_info_by_url($path, $query) {
			$path = ltrim($path, '/');
			$search_url = new Search_Url($path, $query);
			$clean_query_params = array_merge($search_url->get_clean_query_params(), $search_url->get_seo_filters());
			$domain = new Domain();

			$res = new Obj();

			var_dump($search_url);
			die;

			/* get seo attributes */
			$seo_data = Seo::get_seo_attributes($path, $clean_query_params, $search_url->get_category(), $domain->get_city());

			$res->category = $search_url->get_category();
			$res->title = $seo_data['h1'] ? $seo_data['h1'] : $category->title;

			return $res;
		}

	}