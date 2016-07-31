<?php

	class Controller_Block_Category extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		public function action_main_menu()
		{
		    $twig = Twig::factory('block/header/mobile_menu/index');
		    $this->response->body($twig);
		}


		private function object_subquery($city_id) {

			$object_subquery =  DB::select("object.id")
				   ->from("object")
				   ->where("object.active","=","1")
				   ->where("object.is_published","=","1")
				   ->where("object.category","=", DB::expr("category.id") )
				   ->limit(1);


			if ($city_id) {
				$object_subquery = $object_subquery->where("object.city_id","=", $city_id);
			}

			return $object_subquery;
		}

		public static function is_neiboor_city($city_main, $city_other) {
			$result = FALSE;
			$page_type = (isset($GLOBALS['page_type'])) ? $GLOBALS['page_type'] : NULL;
			if (!$page_type) return $result;
			if ( $page_type == 'index' ) return TRUE;

			$city_neiboors_config = Kohana::$config->load("seo.city_neiboors.".$city_main);

			foreach ($city_neiboors_config as $pattern) {

				$find = preg_match($pattern, $city_other);
				if ($find) {
					$result  = TRUE;
					break;
				}

			}

			return $result;
		}

		public static function is_filtered_category($url) {
			$result = FALSE;
			$page_type = (isset($GLOBALS['page_type'])) ? $GLOBALS['page_type'] : NULL;
			if (!$page_type) return $result ;

			$link_seo_config = Kohana::$config->load("seo.category_map_filter.".$page_type);

			foreach ($link_seo_config as $pattern) {

				$find = preg_match($pattern, $url);
				if ($find) {
					$result  = TRUE;
					break;
				}

			}

			return $result;
		}

		public function action_neiboors_map() {
			$view = Twig::factory('block/category/neiboor');

			$domain = new Domain();
			$city = $domain->get_city();
			$current_category = $this->request->post("category");

			$view->seo_segment_url = $this->request->post("seo_segment_url");

			if ($current_category) {
				$view->category_title = $current_category->title;
				$view->category_url = $current_category->seo_name;
			}

			$view->cities = ORM::factory('City')
							->where("is_visible","=",1)
							->where("id","<>",$city->id)
							->cached(Date::WEEK)
							->getprepared_all();

			$view->cities = array_filter($view->cities , function( $item ) use ($city) {
				return Controller_Block_Category::is_neiboor_city($city->seo_name, $item->seo_name);
			});


			$this->response->body($view);
		}

		public function action_category_map() {

			$view = Twig::factory('block/category/through');

			$domain = new Domain();
			$city = $domain->get_city();
			$city_seo_name = $city->seo_name;


			if (!($category_array = Cache::instance('memcache')->get("category_map:{".$city_seo_name."}")))
			{
				$category_array = array();
				$category_list = ORM::factory('Category')
										->where("through_weight","IS NOT",NULL)
										->where("is_ready", "=", 1)
										->order_by("through_weight")
										->find_all();

				foreach ($category_list as $item) {
					
					$childs = ORM::factory('Category')
						->where("parent_id","=",$item->id)
						->where("is_ready", "=", 1)
						->order_by("weight")
						->where(DB::expr('exists'), DB::expr(''), $this->object_subquery($city->id))
						->getprepared_all();

						$childs = array_filter($childs, function( $item ) {
							return !Controller_Block_Category::is_filtered_category($item->url);
						});

					if (count($childs)>0 AND $item->id <> 1)
					{
						$childs_array = array();
						foreach ($childs as $child) {

							$childs_array[$child->id] =  array(
					 			'title' => $child->title,
					 			'url' => '/' . $child->url,
					 			'items' => array(),
					 			'alt' => sprintf ( "Объявления о продаже. %s, в %s", $child->title, $city->sinonim )
					 		);
						}

						$category_array[$item->id] = array(
				 			'title' => $item->title,
				 			'url' => '/' . $item->url,
				 			'items' => $childs_array,
					 		'alt' => sprintf ( "Объявления о продаже. %s, в %s", $item->title, $city->sinonim )
				 		);

					}
					
				}

				$category_array["Другие"] = array(
					42 => "Медицина, здоровье. Товары и услуги",
					156 => "В хорошие руки",
					72 => "Товары для детей"
				);

				Cache::instance('memcache')->set("category_map", $category_array, Date::WEEK);
			}

			$view->data = $category_array;

			$this->response->body($view);

		}

	}