<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_User extends Controller_Rest {

	/**
	 * [action_check_contact validate contact and check if already verified]
	 * @return [void] [
	 *         json code = 200 : contact already verified
	 *         json code = 300 : contact need verify
	 *         json code = 400 : contact with error (blocked, exceed limit sms etc)
	 *		   json code = 500 : contact already verified (but belongs to other user)
	 * ]
	 */
	public function action_check_contact()
	{
		$session_id 		= session_id();
		$contact = (string) $this->post->value;
		$contact_value = trim(strtolower($contact));
		$type = (string) $this->post->type;

		if (!$contact or !$type) {
			$this->json["code"] = 400;
			$this->json['text'] = "Не указаны обязательные параметры";
			return;
		}

		$type_id = Model_Contact_Type::get_type_id($type);
		$contact = ORM::factory('Contact')->by_value($contact_value)->find();

		$validation = $contact->check_contact($session_id, $contact_value, $type_id);

		if (!$validation->check())
		{
			$errors = $validation->errors();

			$this->json['code'] = 400;

			if (isset($errors['contact_mobile'][0]) AND $errors['contact_mobile'][0] == 'contact_already_verified') {
				$this->json['code'] = 500;
			}

			$this->json['text'] = join(", ", $validation->errors('validation/object_form')) ;
			return;
		}

		if ($contact->loaded())
		{
			$verify_validation = $contact->check_verify_contact($session_id);

			if (!$verify_validation->check())
			{
				$this->json["code"] = 300;
				return;
			}

		} else {
			$this->json["code"] = 300;
		}
	}

	/**
	 * [action_sent_code sent code by sms and email, for city phone code returned]
	 * @return [void] [
	 *         json code = 200 : message with code successfully sent
	 *         json code = 300 : contact already verified, for city phone to moderate
	 *         json code = 400 : error
	 * ]
	 */
	public function action_sent_code()
	{
		$session_id 		= session_id();
		$current_user 		= Auth::instance()->get_user();

		$contact = (string) $this->post->value;
		$contact_value = trim(strtolower($contact));
		$type = (string) $this->post->type;
		$type_id = Model_Contact_Type::get_type_id($type);
		$force = FALSE;
		

		if (!$contact_value or !$type or !$type_id) {
			$this->json["code"] = 400;
			$this->json['text'] = "Не указаны обязательные параметры";
			return;
		}

		$type_id = Model_Contact_Type::get_type_id($type);
		$contact = ORM::factory('Contact')->by_value($contact_value)->find();

		$validation = $contact->check_contact($session_id, $contact_value, $type_id, TRUE, TRUE);

		if (!$validation->check())
		{
			if (isset($errors['contact_mobile'][0]) AND $errors['contact_mobile'][0] !== 'contact_already_verified') {
				$this->json["code"] = 400;
				$this->json['text'] = join(", ", $validation->errors('validation/object_form')) ;
				return;
			}
		}

		if ($contact->loaded())
		{
			$verify_validation = $contact->check_verify_contact($session_id);

			if ($verify_validation->check())
			{
				$this->json["code"] = 300;
				return;
			}

		}
		else
		{
			$contact = ORM::factory('Contact')
				->values(array('contact' => $contact_value, 'contact_type_id' => $type_id))
				->create();
		}

		$code = Text::random('numeric', 5);

		if ($contact->loaded())
		{
			
			$contact->verification_code = $code;
			$contact->save();

			if ($type_id == Model_Contact_Type::MOBILE)
			{
				//высылаем код в смс
				$sms_count = ORM::factory('Sms')->cnt_by_phone($contact->contact_clear, $session_id);
				if ($sms_count == 0) {
					$sms = Sms::send($contact->contact_clear, 'Код проверки телефона: '.$code, $session_id, 'sms.from');
				} elseif ($sms_count == 1) {
					$sms = Sms::send($contact->contact_clear, 'Код проверки телефона: '.$code, $session_id, 'sms.from_reserve');
				} else {
					$this->json["code"] = 400;
					$this->json["text"] = "Возникли проблемы с доставкой для Вас кода, обратитесь, пожалуйста, в <a href='http://http://feedback.yarmarka.biz/'>службу техподдержки </a>";
					return;
				}

				
				
				$response = $sms->response;
				$status = $sms->status;
				if ($status == "ERROR")
				{
					$error_code = explode(":", $response);
					$error_code = ( isset($error_code[0]) ) ? intval($error_code[0]) : 600;
					if ($error_code >= 600) {
						$this->json["code"] = 400;
						$this->json["text"] = "Невозможно отправить смс на ваш номер";
						return;
					}

					$this->json["code"] = 400;
					$this->json["text"] = "Невозможно отправить смс на ваш номер по техническим причинам. Письмо с ошибкой отправлено администратору";
					$exception_message = "Смс на номер: ".$contact->contact_clear." не отправлено, по причине: ".$response;
					Email::send(Kohana::$config->load('common.admin_emails'), Kohana::$config->load('email.default_from'), 'Ошибка отправки смс', $exception_message);
					return;
				}
			}
			elseif ($type_id == Model_Contact_Type::PHONE)
			{
				$this->json["code"] = 300;
				$this->json["text"] = " Оператор контакт- центра свяжется с вами для подтверждения номера телефона";

				$contact->verify_for_session($session_id);

				$contact->moderate = 0;
				$contact->save();
			}
			else
			{
				$params = array(
				    'contact' => $contact->contact, 
				    'code' => $code,
				    'domain' => FALSE
				);

				 Email_Send::factory('contact_verification_code')
	    	    			->to( $contact->contact )
	    	    			->set_params($params)
	    	    			->set_utm_campaign('contact_verification_code')
	    	    			->send();
			}
		}
	}

	public function action_check_code()
	{
		$session_id 		= session_id();
		$current_user 		= Auth::instance()->get_user();

		$contact = (string) $this->post->value;
		$contact_value = trim(strtolower($contact));
		$type = (string) $this->post->type;
		$type_id = Model_Contact_Type::get_type_id($type);
		$code = (string) $this->post->code;

		if (property_exists($this->post, 'checkCode')) {
			$code = $this->post->checkCode;
		}
		

		if (!$contact_value or !$type or !$type_id) {
			$this->json["code"] = 400;
			$this->json['text'] = "Не указаны обязательные параметры";
			return;
		}

		$type_id = Model_Contact_Type::get_type_id($type);
		$contact = ORM::factory('Contact')->by_contact_and_type($contact_value, $type_id)->find();

		if (!$contact->loaded() OR !$code) {
			$this->json["code"] = 400;
			$this->json['text'] = "Ошибка при проверке кода";
			return;
		}
		
		if ($code AND $contact->verification_code AND $code === trim($contact->verification_code))
		{
			$this->json['code'] = 200;

			// верифицируем контакт
			$contact->verify_for_session($session_id);

			if ($current_user)
			{
				//убираем из публикации все объявления прежнего владельца
				$prevUser = ORM::factory('User', $contact->verified_user_id);
				foreach ($prevUser->objects->find_all() as $object) {
					$object->set('is_published', 0)->save();
				}

				$contact->verified_user_id = $current_user->id;
			}

			// ставим как отмодерированный
			$contact->moderate = 1;
			$contact->save();

			return;
		}

		$this->json["code"] = 400;
		$this->json['text'] = "Неправильный код";
	}

	function action_email_settings() {
		$type = $this->request->query('type');
		$checked = $this->request->query('checked');

		$user 		= Auth::instance()->get_user();

		if ($user AND $type AND in_array($type, array('news','notices'))) {

			if ($checked === 'false') {

				$setting = ORM::factory('User_Settings')
				                ->get_by_name($user->id, "email_{$type}_off")
				                ->find();
				$setting->user_id = $user->id;
				$setting->name = "email_{$type}_off";
				$setting->value = 1;
				$setting->save();

				$this->json["code"] = 200;
				 return;
			} else {
				ORM::factory('User_Settings')
	                ->get_by_name($user->id, "email_{$type}_off")
	                ->delete_all();

	            $this->json["code"] = 200;
	            return;
			}
		}

		$this->json["code"] = 404;
	}

	function action_check_email() {
		$email = $this->post->email;

		$user = ORM::factory('User')->get_user_by_email($email)->find();

		if ($user->loaded()) {
			$this->json['code'] = 400;
			$this->json['message'] = 'Email уже закреплен за другим пользователем';
			return;
		}

		$contact = ORM::factory('Contact')->where('contact', '=', $email)->find();

		if ($contact->loaded()) {

			if ($contact->verified_user_id == Auth::instance()->get_user()->id) {
				$this->json['code'] = 500;
				$this->json['message'] = 'Данный email уже принадлежит Вам';
				return;
			}

			$this->json['code'] = 300;
			$this->json['message'] = 'Контакт уже принадлежит другому пользователю';
		}
	}

	function action_send_email_code() {

		$email = $this->post->email;

		$user = Auth::instance()->get_user();

		$contact = $user->contacts->where('contact_type_id', '=', Model_Contact_Type::EMAIL)->find();
		$code = Text::random();
		$contact->verification_code = $code;
		$contact->save();

		$params = array(
		    'contact' => $email, 
		    'code' => $code,
		    'domain' => FALSE
		);

		Email_Send::factory('contact_verification_code')
	    	->to( $email )
	    	->set_params($params)
	    	->set_utm_campaign('contact_verification_code')
	    	->send();

	    $this->json['message'] = 'Код успешно выслан. Введите его в поле для смены email';
	}

	function action_check_email_code() {

		$code = $this->post->code;
		$email = $this->post->email;

		if (empty($email)) {
			$this->json['code'] = 400;
			$this->json['message'] = 'Произошла ошибка на сервере. Попробуйте повторить операцию заново';
			return;
		}

		$user = Auth::instance()->get_user();

		$contact = $user->contacts->where('contact_type_id', '=', Model_Contact_Type::EMAIL)->find();
		
		if ($contact->verification_code == $code AND $user->id == $contact->verified_user_id) {
			$contact->values(array(
				'verification_code' => '',
				'contact' => $email,
				'contact_clear' => $email
			))->save();

			$user->email = $email;
			$user->save();

			Auth::instance()->logout();
			$this->json['message'] = '/user/login?return=user/userinfo';
			return;
		}

		$this->json['code'] = 500;
	    $this->json['message'] = 'Неверный пароль';
	}
}
