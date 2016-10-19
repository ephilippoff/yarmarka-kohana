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

		public function get_enabled() {
			return $this->where('filters','IS NOT',NULL)
                        ->where('user_id','IS NOT',NULL)
                        ->where('enabled','=',1)
                        ->find_and_map(function($item){
                             
                             $result = $item->get_row_as_obj();
                             $result->data = unserialize($item->data);
                             $result->filters = json_decode(json_encode(json_decode($item->filters)), True);

                             return $result;
                        });
		}

	}