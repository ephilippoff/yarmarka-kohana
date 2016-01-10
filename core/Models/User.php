<?php

	namespace Yarmarka\Models;

	class User {

		const ADMIN_ROLE = 1;
		const MODERATOR_ROLE = 9;

		private $_role;
		private $_isGuest;
		private $_email;

		public function __construct() {
			$this->_role = NULL;
		}

		/* getters */

		public function getEmail() {
			return $this->_email;
		}

		public function getRole() {
			return $this->_role;
		}

		public function getIsGuest() {
			return $this->_isGuest;
		}

		/* role checks */

		public function isAdmin() {
			return $this->checkRole(self::ADMIN_ROLE);
		}

		public function isModerator() {
			return $this->checkRole(self::MODERATOR_ROLE);
		}

		public function isAdminOrModerator() {
			return $this->isAdmin() || $this->isModerator();
		}

		public function checkRole($roleValue) {
			return $this->_role == $roleValue;
		}

		/* static functions */

		public static function current() {
			$instance = new self();
			/* Kohana dependent code */
			$kohanaUserInstance = \Auth::instance()->get_user();
			if ($kohanaUserInstance) {
				$instance->_role = $kohanaUserInstance->role;
				$instance->_isGuest = false;
				$instance->_email = $kohanaUserInstance->email;
			} else {
				$instance->_isGuest = true;
				$instance->_email = NULL;
			}
			return $instance;
		}

	}