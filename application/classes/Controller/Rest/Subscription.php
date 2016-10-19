<?php

	class Controller_Rest_Subscription extends Controller_Rest {

		protected $user = NULL;

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

		protected function check_user() {
			if (($this->user = Auth::instance()->get_user()) == NULL) {
				throw new Exception('get_user() == NULL');
			}
		}

		protected function get_subscription_info() {
			$parsed_referer = $this->get_parsed_referer();
			if (!array_key_exists('query', $parsed_referer)) {
				$parsed_referer['query'] = NULL;
			}
			$parsed_query = array();
			parse_str($parsed_referer['query'], $parsed_query);
			$res = Subscription_Manage::get_subscription_info_by_url($parsed_referer['path'], $parsed_query);
			$res->query = $parsed_referer['query'];
			$res->path = $parsed_referer['path'];
			return $res;
		}

		protected function check_exists() {
			$this->post->error = null;
			$model = ORM::factory('Subscription_Surgut')
				->find_by_user($this->user->id)
				->where('query', '=', $this->post->info->query)
				->where('path', '=', $this->post->info->path)
				->find();
			if ($model->loaded()) {
				$this->post->error = 'Данная подписка уже существует!';
				return true;
			}
			return false;
		}

		public function action_save_confirm() {

			$this->check_user();
			$this->post->info = $this->get_subscription_info();
			$this->check_exists();
			$this->json = $this->post;
		}

		public function action_save() {

			$this->check_user();
			$this->post->info = $this->get_subscription_info();
			if (!$this->check_exists()) {

				$filters = json_decode(json_encode(json_decode($this->post->filters)), True);
				$filters['order'] = 'id';

				$main_search_query = Search::searchquery($filters, array( 
					"limit" => 1,
					"page" => 1
				));
				
				$main_search_result = Search::getresult($main_search_query->execute()->as_array());

				$last_object_id = NULL;
				if (count($main_search_result)) {
					$last_object_id = $main_search_result[0]['id'];
				}

				$model = ORM::factory('Subscription_Surgut');
				$model->set_data($this->post->info);
				$model->user_id = $this->user->id;
				$model->created = date('Y-m-d H:i:s');
				$model->query = $this->post->info->query;
				$model->path = $this->post->info->path;
				$model->filters = $this->post->filters;
				$model->last_object_id = $last_object_id;
				$model->enabled = 1;
				$model->save();

			}

			$this->json = $this->post;

		}

	}