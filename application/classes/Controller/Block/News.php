<?php

	class Controller_Block_News extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		public static function get_items(
			$categories, 
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
			$countQuery = $objectsService->getObjects(array('data_list.value', 'data_list_value'));
			$objectsService->selectPublished($countQuery);
			$objectsService->filterDataList($countQuery, array_keys($categories));
			$countQuery
				->select(array(DB::expr('count(*)'), 'cnt'))
				->group_by('data_list.value');
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
				// var_dump($group); die;
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

			$view->catTitle = $this->request->post("catTitle");
			$view->reverse = $this->request->post("reverse");
			$view->newsTitle = $this->request->post("newsTitle");
			$itemsPerCategory = $this->request->post("itemsPerCategory");
			$view->isNewsSubcategory = $this->request->post("isNewsSubcategory");
			$view->onPageFlag = $this->request->post("onPageFlag");

			$categories = self::get_categories();
			$newsGroups = self::get_items($categories, $itemsPerCategory);

			// update total pages value
			foreach($newsGroups as &$newsGroup) {
				$newsGroup['pages'] = ceil($newsGroup['count'] / $itemsPerCategory);
			} 		

			// echo '<pre>'; var_dump($view); echo '</pre>'; die;
			
			/* push view data */
			$view->data = $newsGroups;

			$this->response->body($view);


		}

	}