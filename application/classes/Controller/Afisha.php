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

		public function action_cities() {
			$this->json['code'] = 200;
			$rawData = $this->api->getCities();
			$this->json['data'] = $rawData['List'];
			$this->response->body(json_encode($this->json));
		}

	}

?>