<?php

	class Controller_Block_Advert extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		protected function process_item($item) {
			$objectsService = $this->getService('Objects');
			return array(
						'image' => 'http://yarmarka.biz' . Imageci::getThumbnailPath($item['main_image_filename'], '208x208'),
						'date' => strtotime($item['date_expiration']),
						'title' => $item['title'],
						'url' => $objectsService->getUrl($item),
						'price' => $item['price'],
						'cat_title' => $item['category_title'],
						'cat_url' => $item['category_url'],
						'short_text' => mb_strlen($item['full_text']) > 150 ? (mb_substr($item['full_text'], 0, 147) . '...') : $item['full_text']
					);
		}

		public function action_main_page_premium() {

			/* settings */
			$count = 5;

			$view = Twig::factory('block/advert/main_page_premium');

			/* get services */
			$objectsService = $this->getService('Objects');

			/* get data */
			$query = $objectsService->getObjects();
			$objectsService->selectMainImage($query);
			$objectsService->selectPublished($query);
			$objectsService->filterOnlyPremium($query);
			$objectsService->selectCategoryUrl($query);
			$items = $query->execute();

			/* process data */
			$processedData = array();
			foreach($items as $index => $item) {
				if ($index >= $count) {
					break;
				}
				$processedData []= $this->process_item($item);
			}

			/* push view data */
			$view->data = $processedData;

			$this->response->body($view);

		}


		public function action_main_page_vip() {

			/* settings */
			$count = 30;

			$view = Twig::factory('block/advert/main_page_latest');

			/* get services */
			$objectsService = $this->getService('Objects');

			/* get city seo name from url */

			$domain = new Domain();
        	$city = $domain->get_city_by_subdomain($domain->get_subdomain());
			$citySeoName = $city->seo_name;

			/* get data */
			$query = $objectsService->getObjects();
			$objectsService->selectMainImage($query);
			$objectsService->selectPublished($query);
			$objectsService->filterByCitySeoName($query, $citySeoName);
			$objectsService->filterOnlyVip($query);
			$objectsService->selectCategoryUrl($query);
			$items = $query->execute();

			/* process data */
			$processedData = array();
			foreach($items as $index => $item) {
				if ($index >= $count) {
					break;
				}
				$processedData []= $this->process_item($item);
			}

			/* push view data */

			$count = count($processedData);

			if ($count < 8) {
				$new_data = $this->main_page_latest($count, $processedData);
				$view->data = $new_data;
			}else{
				shuffle($processedData);
				array_slice($processedData, 0, 8);
				$view->data = $processedData;
			}



			$this->response->body($view);

		}

		public function main_page_latest($limit, $vip_data) {

			/* settings */
			$count = 8 - $limit;
			$rubric = 1;
			$categoryHierarchyLevel = 5;

			$view = Twig::factory('block/advert/main_page_latest');

			$categoriesService = $this->getService('Categories');
			$hierarchy = $categoriesService->getCategoryWithChilds(
				$rubric, $categoryHierarchyLevel, array( 'fresh_limit' ));

			usort($hierarchy, function ($a, $b) {
				return $b['fresh_limit'] - $a['fresh_limit'];
			});			

			/* get services */
			$objectsService = $this->getService('Objects');

			/* get city seo name from url */
			$citySeoName = 'tyumen';
			$uriMatches = array();
			if (preg_match('/(.*).' . Kohana::$config->load('common.main_domain') . '/', $_SERVER['HTTP_HOST'], $uriMatches)) {

				$citySeoName = $uriMatches[1];

			}

			if (!($items = Cache::instance('memcache')->get("main_page_latest:{$citySeoName}")))
			{

				/* prepare the query */
				$baseQuery = NULL;

				foreach($hierarchy as $category) {

					if ($category['fresh_limit'] < 1) {
						continue;
					}

					$query = $objectsService->getObjects();
					$objectsService->selectMainImage($query, true);
					$objectsService->selectPublished($query);
					$objectsService->selectShowOnMain($query);
					$objectsService->filterPassedModeration($query);
					$objectsService->filterByCitySeoName($query, $citySeoName);
					$objectsService->selectCategoryUrl($query);
					$objectsService->withCategories($query, array_merge($category['childs'], array( $category['id'] )));
					$objectsService->orderByCreated($query);
					$query->limit($category['fresh_limit']);
					$query = DB::select('*')->from(array(DB::expr('(' . $query . ')'), 'x'));

					if ($baseQuery !== NULL) {
						$baseQuery->union($query);
					} else {
						$baseQuery = $query;
					}
				}
				
				//var_dump((string) $baseQuery);
				//die;

				$items = $baseQuery === NULL ? array() : $baseQuery->execute();

				Cache::instance('memcache')->set("main_page_latest:{$citySeoName}", $items, 1200);
			}
			

			/* process data */
			$processedData = array();
			foreach($items as $index => $item) {
				if ($index >= $count) {
					break;
				}
				$processedData []= $this->process_item($item);
			}

			$new_data = array_merge($vip_data, $processedData);


			return $new_data;

			/* push view data */
			// $view->data = $processedData;

			// $this->response->body($view);
		}

	}