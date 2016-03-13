<?php

	class Model_Stat extends ORM {

		protected $_table_name = 'user_object_stats';

		protected $_belongs_to = array(
				'user' => array('model' => 'User', 'foreign_key' => 'user_id'),
				'object' => array('model' => 'Object', 'foreign_key' => 'object_id')
			);

		public function start($user_id, $object_id) {
			if ($this->loaded()) {
				throw new Exception('Cannot start statistic when loaded');
			}

			$this->user_id = $user_id;
			$this->object_id = $object_id;
			$this->date_start = time();
			$this->date_end = NULL;

			$this->save();

			return $this;
		}

		public function end() {
			if (!$this->loaded()) {
				throw new Exception('Nothing to end');
			}

			if ($this->date_end !== NULL) {
				throw new Exception('Finalized');
			}

			$this->date_end = time();
			$this->save();

			return $this;
		}

	}

?>