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

		public function action_main_page_latest() {

			/* settings */
			$count = 8;

			$view = Twig::factory('block/advert/main_page_premium');

			$rubric = 14;

			$categoriesService = $this->getService('Categories');

			$hierarchy = $categoriesService->getCategoryWithChilds($rubric);

			/* get services */
			$objectsService = $this->getService('Objects');

			/* get data */
			$query = $objectsService->getObjects();
			$objectsService->selectMainImage($query, true);
			$objectsService->selectPublished($query);
			$objectsService->filterPassedModeration($query);
			$objectsService->selectCategoryUrl($query);
			$objectsService->withCategories($query, $hierarchy);
			$objectsService->orderByCreated($query);
			$items = $query->limit($count)->execute();

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

	}