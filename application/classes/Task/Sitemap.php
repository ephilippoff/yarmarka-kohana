<?php

	class Task_Sitemap extends Minion_Task {

		protected function _execute(array $params) {
			ob_end_clean();
			$s = new Sitemap('surgut');
			$s->rebuild();

			$s = new Sitemap('tobolsk');
			$s->rebuild();
			echo 'OK';
		}

	}

?>