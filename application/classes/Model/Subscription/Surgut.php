<?php

	class Model_Subscription_Surgut extends ORM {

		protected $_table_name = 'subscription_surgut';

		public function get_data() {

			if ($this->loaded()) {
				return unserialize($this->data);
			} else {
				return null;
			}
			
		}

		public function set_data($obj) {

			$this->data = serialize($obj);

		}

		public function get_not_enabled() {
			if ($this->loaded()) {
				return !$this->enabled;
			} else {
				return 0;
			}
		}

		public function check_is_mine() {
			$user = Auth::instance()->get_user();
			if ($this->loaded() && $user != NULL) {
				return $user->id == $this->user_id;
			} else {
				return false;
			}
		}

		public function find_by_user($id) {
			return $this->where('user_id', '=', $id);
		}

	}