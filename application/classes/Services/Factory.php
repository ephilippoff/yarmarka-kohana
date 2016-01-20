<?php

	class Services_Factory {

		private static $_instances = array();

		public static function instance($className) {
			if (!array_key_exists($className, self::$_instances)) {
				self::$_instances[$className] = self::factory($className);
			}
			return self::$_instances[$className];
		}

		public static function factory($className) {
			$className = 'Services_' . $className;
			return new $className();
		}

	}