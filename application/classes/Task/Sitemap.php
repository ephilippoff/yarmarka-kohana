<?php

	class Task_Sitemap extends Minion_Task {

		protected function _execute(array $params) {
			ob_end_clean();

			// $s = new Sitemap('surgut');
			// $s->rebuild();

			$main_c = ORM::factory('City',1);
			$s = new Sitemap($main_c->seo_name);
			$s->rebuild();

			echo 'OK main';

			$cities = ORM::factory('City')->where('is_visible','=',1)->find_all();
			foreach ($cities as $city) {

				$s = new Sitemap($city->seo_name);
				$s->rebuild();
				echo 'OK '.$city->seo_name;
			}
			
		}

	}

?>