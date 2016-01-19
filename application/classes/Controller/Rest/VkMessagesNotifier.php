<?php

	class Controller_Rest_VkMessagesNotifier extends Controller_Rest {

		protected $required = array( 'num', 'last_comment', 'date', 'sign' );

		protected function check_params() {
			foreach($this->required as $key) {
				if (!property_exists($this->post, $key) || !$this->post->{$key}) {
					throw new Exception($key);
				}
			}
		}

		protected function check_sign() {

			//debug
			//return;

			$app_secret = Kohana::$config->load('common.vk_app_secret');
			$signCalculated = md5(implode('', array( $app_secret, $this->post->date, $this->post->num, $this->post->last_comment )));
			if ($signCalculated != $this->post->sign) {
				throw new Exception('Bad sign');
			}
		}

		protected function get_referer() {
			//debug
			//return 'http://surgut.local.yarmarka.biz/avtotransport/legkovye-avtomobili/prodazha-acura-el-1977gv-akpp-vnedorozhnik-4036745.html';

			return $_SERVER['HTTP_REFERER'];
		}

		protected function get_object_id() {
			$matches = array();
			$str = $this->get_referer();

			if (preg_match_all('/^(.*)-(\d+).html$/', $str, $matches) && count($matches[2]) > 0) {
				return (int) $matches[2][0];
			}

			return NULL;
		}

		public function action_submit() {

			try {
				$this->check_params();
				$this->check_sign();

				$object_id = $this->get_object_id();

				/* search object */
				$object = ORM::factory('Object', $object_id);
				if (!$object->loaded()) {
					throw new Exception('Object not found!');
				}

				/* get author */
				$author = ORM::factory('User', $object->author);
				if (!$author->loaded()) {
					throw new Exception('Author not found!');
				}

				/* send email */
				if (!$author->email) {
					throw new Exception('Author have no email!');
				}
				

				$message = "К Вашему объявлению был добавлен новый комментарий ВКонтакте. \r\n";
				$message .= "Ссылка на объявление: " . $object->get_full_url() . "\r\n";
				$message .= "Дата коментария: " . date('d.m.Y H:i:s', strtotime($this->post->date)) . "\r\n";
				$message .= 'Текст комментария:' . "\r\n";
				$message .= '-----------------------------------------' . "\r\n";
				$message .= htmlspecialchars($this->post->last_comment) . "\r\n";
				$message .= '-----------------------------------------' . "\r\n";

				/* prepare subject */
				$subject = 'У Вас сообщение на «Ярмарка-онлайн»';

				Email::send( $author->email, Kohana::$config->load('email.default_from'), $subject, $message, false);

				$this->json = $this->post;
			} catch (Exception $e) {
				var_dump($e->getMessage());
				die;
			}

		}

	}