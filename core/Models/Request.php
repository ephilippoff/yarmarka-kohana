<?php

	namespace Yarmarka\Models;

	class Request {

		/* private values */
		private $_url;

		/* ctor */
		public function __construct() {

		}

		/* getters */
		public function getUrl() {
			return $this->_url;
		}

		/* setters */
		public function setUrl($value) {
			$this->_url = ltrim($value, '/');
		}

		/* current instance */
		private static $_current = NULL;

		public static function current() {
			if (self::$_current === NULL) {
				self::$_current = new self();
			}
			return self::$_current;
		}

	}