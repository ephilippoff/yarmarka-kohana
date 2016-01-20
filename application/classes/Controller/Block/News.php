<?php

	class Controller_Block_News extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		public function action_main_page() {
			/* settings */
			$shortTextLength = 250;
			$shortTextAfter = '...';
			$itemsPerCategory = 5;

			$view = Twig::factory('block/news/main_page');

			/* get services instances */
			$attribitesService = $this->getService('Attributes');
			$objectsService = $this->getService('Objects');

			/* load categories */
			$items = $attribitesService
				->getElementsByAttributeSeoNameQuery('news-category')
				->find_all();
			$categories = array();
			foreach($items as $item) {
				$categories[$item->id] = array(
						'title' => $item->title,
						'url' => '/novosti/' . $item->url
					);
			}

			/* load news */
			$query = $objectsService->getObjects();
			$objectsService->selectPublished($query);
			$objectsService->selectMainImage($query);
			$objectsService->filterDataList($query, array_keys($categories));
			$query->order_by('date_expired', 'desc');
			$items = $query->execute();

			$newsGroups = array();

			foreach($items as $item) {
				$sci = $item['data_list_value'];
				if (!array_key_exists($sci, $newsGroups)) {
					$newsGroups[$sci] = array(
							'title' => $categories[$sci]['title'],
							'url' => $categories[$sci]['url'],
							'items' => array()
						);
				}
				$group = &$newsGroups[$sci];

				if (count($group['items']) >= $itemsPerCategory) {
					continue;
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

				$group['items'] []= array(
						'image' => 'http://yarmarka.biz' . Imageci::getThumbnailPath(
							$item['main_image_filename'], '208x208'),
						'date' => strtotime($item['date_expired']),
						'title' => $item['title'],
						'short_text' => $shortText,
						'url' => '/novosti/' . $item['seo_name'] . '-' . $item['id'] . '.html'
					);
			}

			

			//echo '<pre>'; var_dump($newsGroups); echo '</pre>';
			
			/* push view data */
			$view->data = $newsGroups;

			$this->response->body($view);

		}

	}