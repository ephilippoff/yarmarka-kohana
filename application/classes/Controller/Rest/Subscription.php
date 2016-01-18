<?php

	class Controller_Rest_Subscription extends Controller_Rest {

		protected function get_referer() {
			if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
				throw new Exception('Referer header not present');
			}
			return $_SERVER['HTTP_REFERER'];
		}

		protected function get_parsed_referer() {
			$parsed_referer = parse_url($this->get_referer());
			if (!$parsed_referer) {
				throw new Exception('Error while parse Referer header');
			}		
			return $parsed_referer;	
		}

		public function action_save_confirm() {

			if (Auth::instance()->get_user() == NULL) {
				throw new Exception('get_user() == NULL');
			}

			$parsed_referer = $this->get_parsed_referer();
			$parsed_query = array();
			parse_str($parsed_referer['query'], $parsed_query);

			$this->post->info = Subscription_Manage::get_subscription_info_by_url($parsed_referer['path'], $parsed_query);

			$this->json = $this->post;
			
		}

	}