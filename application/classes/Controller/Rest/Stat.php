<?php

	class Controller_Rest_Stat extends Controller_Rest {

		public function action_end() {

			$user = Auth::instance()->get_user();
			$x = ORM::factory('Stat', $this->post->id);
			if ($user === NULL || !$x->loaded() || $x->user_id != $user->id) {
				$this->json['error'] = true;
				return;
			}

			$x->end();

		}

	}

?>