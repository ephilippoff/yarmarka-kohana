<?php

	defined('SYSPATH') or die('No direct script access.');

	class Controller_Afisha extends Controller_Template {

		protected $api = NULL;

		public function before() {
			$this->use_layout   = FALSE;
			$this->auto_render  = FALSE;

			$this->api = RamblerApi::factory();
		}

		public function action_index() {
			$twig = Twig::factory('afisha/index');
			$twig->params = array();


			$this->response->body($twig);
		}

		public function action_city() {
			$this->json['code'] = 200;
			$rawData = $this->api->getCities();
			$this->json['data'] = $rawData['List'];
			$this->response->body(json_encode($this->json));
		}

		public function action_object_type() {
			$this->json['code'] = 200;
			$this->json['data'] = $this->getTypes();
			$this->response->body(json_encode($this->json));
		}		

		public function action_object() {
			$checkRes = $this->checkIntGt0(array( 'cityId' ), $_GET);
			$checkRes = $checkRes && $this->checkObjectType(array( 'objectTypeId' ), $_GET);

			if ($checkRes) {
				$this->json['code'] = 200;
				$rawData = $this->api->getObjects($_GET['objectTypeId'], (int) $_GET['cityId']);
				$this->json['data'] = $rawData['List'];
			}

			$this->response->body(json_encode($this->json));
		}

		protected function getTypes() {
			$res = $this->api->getTypes();
			return $res['List'];
		}

		protected function checkObjectType($params, $arr) {
			return $this->checkStringsIn($params, $arr, array_map(function ($item) { return $item['Name']; }, $this->getTypes()));
		}

		protected function checkStringsIn($params, $arr, $allowed) {
			return $this->check($params, $arr, function ($val) use ($allowed) { return in_array($val, $allowed); });
		}

		protected function checkIntGt0($params, $arr) {
			return $this->check($params, $arr, function ($val) { return ((int)$val) > 0; });
		}

		protected function check($params, $arr, $check = NULL, $checkMessage = 'Bad value') {
			foreach($params as $key) {
				if (empty($arr[$key])) {
					$this->json['code'] = 400;
					$this->json['error'] = 'Require ' . $key;
					return false;
				}

				if ($check !== NULL && !$check($arr[$key])) {
					$this->json['code'] = 400;
					$this->json['error'] = $checkMessage . ': ' . $key;
					return false;
				}
			}

			return true;
		}
	}

?>