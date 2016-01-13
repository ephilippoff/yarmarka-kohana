<?php

	class Controller_Rest_Complain extends Controller_Rest {

		protected function getSubjects() {
			return array(
					array( 'id' => 0, 'title' => 'Неправильные контакты' ),
					array( 'id' => 1, 'title' => 'Объявление устарело' ),
					array( 'id' => 2, 'title' => 'Оскорбительное содержание' ),
					array( 'id' => 3, 'title' => 'Мошенничество' ),
					array( 'id' => 4, 'title' => 'Нарушение закона' ),
					array( 'id' => 5, 'title' => 'Другое' ),
				);
		}

		public function action_subject() {
			$this->json = $this->getSubjects();
		}

		public function action_send() {

			/* get user */
			$user = Auth::instance()->get_user();

			/* validate request data */
			$data = $this->post;
			$this->json = &$data;
			$validationResult = array();

			$object = NULL;
			if (!$data->objectId) {
				$validationResult []= 'Не указан объект';
			} else {
				$object = ORM::factory('Object', $data->objectId);
				if (!$object->loaded()) {
					$validationResult []= 'Объект не найден';
				}
			}

			if (!$data->subject['id']) {
				$validationResult []= 'Не указана категория жалобы';
			}
			$subjects = $this->getSubjects();
			if (!array_key_exists($data->subject['id'], $subjects)) {
				$validationResult []= 'Категория жалобы не существует';
			}
			$subject = $subjects[$data->subject['id']];

			if (!$data->message) {
				$validationResult []= 'Сообщение не может быть пустым';
			}

			$userEmailToCheck = NULL;
			if ($user == NULL) {
				if (!$data->userEmail) {
					$validationResult []= 'Нужно указать email';
				} else {
					if (!Valid::email($data->userEmail)) {
						$validationResult []= 'Неправильный формат email';
					} else {
						$userEmailToCheck = $data->userEmail;
					}
				}

				if (!$data->userName) {
					$validationResult []= 'Нужно указать имя';
				}
			} else {
				$userEmailToCheck = $user->email;
			}

			if ($userEmailToCheck != NULL && $object != NULL && $object->loaded()) {
				/* check if user already sent complaint */
				$check = DB::select()
					->from('complaints_v2')
					->where('email', '=', $userEmailToCheck)
					->where('object_id', '=', $object->id)
					->execute()
					->as_array();
				if (!empty($check)) {
					$validationResult []= 'С этого email уже была отправлена жалоба для этого объявления';
				}
			}

			if (!empty($validationResult)) {
				$data->validationMessage = implode('<br />', $validationResult);
				return;
			}

			/* set send flag */
			DB::insert('complaints_v2', array( 'email', 'object_id'))
				->values(array( $userEmailToCheck, $object->id ))
				->execute();

			/* prepare message text */
			$message = "На сайте поступила жалоба на объявление:\r\n"; 
			$message .= "Ссылка на объявление: " . $object->get_full_url() . "\r\n";
			$message .= "Категория жалобы: " . $subject['title'] . "\r\n";
			if ($user == NULL) {
				$message .= "Имя пользователя: " . htmlspecialchars($data->userName) . "\r\n";
				$message .= "Email пользователя: " . htmlspecialchars($data->userEmail) . "\r\n";
			} else {
				$message .= "Email пользователя: " . $user->email . "\r\n";
			}
			$message .= "Текст жалобы: ---------------------\r\n";
			$message .= htmlspecialchars($data->message) . "\r\n";
			$message .= "-----------------------------------\r\n";

			/* prepare subject */
			$subject = 'Жалоба на объявление на сайте "Ярмарка"';

			/* send email */
			Email::send(
				Kohana::$config->load('common.send_complaints_to'), 
				Kohana::$config->load('email.default_from'), 
				$subject, 
				$message, 
				false);

			/* update model and return */
			$data->userName = $user != NULL ? $user->email : htmlspecialchars($data->userName);
		}

		/*

		create table complaints_v2 (
			id serial not null primary key,
			object_id int not null,
			email varchar(255) not null
		)

		*/

	}