<?php

	class LastViews {

		protected $session = NULL;
		protected $data = array();

		public function __construct() {
			$this->session = Session::instance();
			$this->data = $this->session->get('last_views');

			if (!$this->data) {
				$this->data = array();
			}
		}

		public function remove($objectId) {
			while(($key = array_search($objectId, $this->data)) !== false) {
				array_splice($this->data, $key, 1);
			}
			return $this;
		}

		public function set($objectId) {
			$this->remove($objectId);
			$this->data []= $objectId;
			return $this;
		}

		public function get() {
			return $this->data;
		}

		public function commit() {
			$this->session->set('last_views', $this->data);
			return $this;
		}

		public function clear() {
			$this->data = array();
			return $this;
		}

		//default instance
		private static $_instance = NULL;
		public static function instance() {
			if (self::$_instance === NULL) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}