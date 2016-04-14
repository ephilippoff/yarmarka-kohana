<?php

	class Task_Sitemap extends Minion_Task {

		protected function _execute(array $params) {
			ob_end_clean();
			$s = new Sitemap();
			$s->rebuild();
			echo 'OK';
		}

	}

?>