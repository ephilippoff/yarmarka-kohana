<?php

	class Controller_Block_Advert extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
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
				$processedData []= array(
						'image' => 'http://yarmarka.biz' 
							. Imageci::getThumbnailPath(
								$item['main_image_filename'], '208x208'),
						'date' => strtotime($item['date_expiration']),
						'title' => $item['title'],
						'url' => $objectsService->getUrl($item),
						'price' => $item['price']
					);
			}

			/* push view data */
			$view->data = $processedData;

			$this->response->body($view);

		}

	}