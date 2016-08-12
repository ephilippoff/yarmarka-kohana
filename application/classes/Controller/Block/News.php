<?php

	class Controller_Block_News extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		public static function get_items(
			$categories,
			$city_id,
			$itemsPerCategory = NULL,
			$usePagination = false,
			$page = 1,
			$perPage = 6,
			$groupByCategories = true,
			$shortTextLength = 300, 
			$shortTextAfter = '...'
		) {

			// create service
			$objectsService = Services_Factory::instance('Objects');

			// prepare pagination parameters
			$offset = ($page - 1) * $perPage;
			$limit = $perPage;

			// fill counts for categories
			$countQuery = DB::select(DB::expr('"data_list"."value" AS "data_list_value", sum((select count(*) from object where "data_list"."object" = "object"."id" and "object"."is_published" = 1 AND "object"."date_expired" <= NOW())) AS "cnt" FROM "data_list" WHERE "data_list"."value" IN (' . implode(',', array_keys($categories)) . ') group by "data_list"."value"'));
			$counts = $countQuery->execute();
			foreach($counts as $count) {
				$sci = $count['data_list_value'];
				if (array_key_exists($sci, $categories)) {
					$categories[$sci]['count'] = $count['cnt'];
				}
			}

			// prepare base query
			$query = $objectsService->getObjects();
			$objectsService->selectPublished($query);
			$objectsService->selectMainImage($query);
			$objectsService->filterDataList($query, array_keys($categories));

			if ($city_id !== 1) {
				if ($city_id) {
					$query = $query->where(DB::expr($city_id), "=", DB::expr("ANY(object.cities)"));
				}
			}

			$query->order_by('date_expired', 'desc');
			if ($usePagination) {
				$query
					->offset($offset)
					->limit($limit);
			}
			$items = $query->execute();

			// process items
			$newsGroups = array();
			foreach($items as $item) {
				if ($groupByCategories) {
					$sci = $item['data_list_value'];
					if (!array_key_exists($sci, $newsGroups)) {
						$newsGroups[$sci] = array(
								'title' => $categories[$sci]['title'],
								'url' => $categories[$sci]['url'],
								'id' => $categories[$sci]['id'],
								'items' => array(),
								'count' => $categories[$sci]['count']
							);
					}

					$group = &$newsGroups[$sci]['items'];
					if ($itemsPerCategory !== NULL && count($group) >= $itemsPerCategory) {
						continue;
					}
				} else {
					$group = &$newsGroups;
				}

				/* prepare short text */
				$shortText = $item['full_text'];
				if (mb_strlen($shortText) > $shortTextLength) {
					$shortText = mb_substr(
							$shortText, 
							0, 
							$shortTextLength - mb_strlen($shortTextAfter)) 
						. $shortTextAfter;
				}

				$group []= array(
						'image' => 'http://yarmarka.biz' . Imageci::getThumbnailPath(
							$item['main_image_filename'], '208x208'),
						'date' => strtotime($item['date_expired']),
						'title' => $item['title'],
						'short_text' => $shortText,
						'url' => '/novosti/' . $item['seo_name'] . '-' . $item['id'] . '.html'
					);
			}
			return $newsGroups;

		}

		public static function get_categories($id = NULL) {

			// get service
			$attribitesService = Services_Factory::instance('Attributes');

			// make query
			$query = $attribitesService->getElementsByAttributeSeoNameQuery('news-category');
			if ($id !== NULL) {
				$query->where('attribute_element.id', '=', $id);
			}

			// process query results
			$categories = array();
			$items = $query->find_all();
			foreach($items as $item) {
				$categories[$item->id] = array(
						'title' => $item->title,
						'url' => '/novosti/' . $item->url,
						'id' => $item->id,
					);
			}

			return $categories;
		}

		public function action_main_page() {


			$view = Twig::factory('block/news/main_page');

			$citySeoName = 'tyumen';
			$uriMatches = array();
			if (preg_match('/(.*).' . Kohana::$config->load('common.main_domain') . '/', $_SERVER['HTTP_HOST'], $uriMatches)) {

				$citySeoName = $uriMatches[1];

			}

			$view->catTitle = $this->request->post("catTitle");
			$view->reverse = $this->request->post("reverse");
			$view->newsTitle = $this->request->post("newsTitle");
			$itemsPerCategory = $this->request->post("itemsPerCategory");
			$view->isNewsSubcategory = $this->request->post("isNewsSubcategory");
			$view->onPageFlag = $this->request->post("onPageFlag");
			$view->seo_attributes = $this->request->post("seo_attributes");

			$city_id = $this->request->post("city_id");

			$cache = Cache::instance('memcache');
			if (!($categories = $cache->get("main_page_news_cat:{$citySeoName}"))) {
				$categories = self::get_categories();
				$cache->set("main_page_news_cat:{$citySeoName}", $categories, 3600);
			}
			if (!($newsGroups = $cache->get("main_page_news_items:{$city_id}"))) {
				$newsGroups = self::get_items($categories, $city_id, $itemsPerCategory);
				$cache->set("main_page_news_items:{$city_id}", $newsGroups, 3600);
			}
			// update total pages value
			foreach($newsGroups as &$newsGroup) {
				$newsGroup['pages'] = ceil($newsGroup['count'] / $itemsPerCategory);
			} 		
			
			/* push view data */
			$view->data = $newsGroups;


			$this->response->body($view);


		}

	}