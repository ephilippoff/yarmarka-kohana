<?php

	class Controller_Block_Category extends Controller_Block {

		public function before() {
			parent::before();

			$this->auto_render = false;
		}

		public function action_main_page() {

			$view = Twig::factory('block/category/main_page');

			/* get services */
			$categoriesService = $this->getService('Categories');

			/* get top categories from database */
			$query = $categoriesService->getCategories();
			$categoriesService->selectTopLevel($query);
			$items = $query->execute();

			/* process top level categories */
			$topCategories = array();
			foreach($items as $item) {
				$topCategories[$item['id']] = array(
						'title' => $item['title'],
						'url' => '/' . $item['url'],
						'image' => 'http://yarmarka.biz/images/min_' . $item['main_menu_icon'],
						'items' => array(),
					);
			}

			/* get lvl1 categories */
			$query = $categoriesService->getCategories();
			$categoriesService->filterByParentIds(
				$query, array_keys($topCategories));
			$items = $query->execute();

			/* process data */
			foreach($items as $item) {
				$topCategories[$item['parent_id']]['items'] []= array(
						'title' => $item['title'],
						'url' => '/' . $item['url'],
						//'image' => '/images/' . $item['main_menu_icon'],
						'items' => array()
					);
			}

			/* push view data */
			$view->data = $topCategories;

			//echo '<pre>'; var_dump($view->data); echo '</pre>';

			$this->response->body($view);

		}

	}