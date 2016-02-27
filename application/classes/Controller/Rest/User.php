<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_User extends Controller_Rest {

	/**
	 * [action_check_contact validate contact and check if already verified]
	 * @return [void] [
	 *         json code = 200 : contact already verified
	 *         json code = 300 : contact need verify
	 *         json code = 400 : contact with error (blocked, exceed limit sms etc)
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
			$this->json["code"] = 400;
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
			$this->json["code"] = 400;
			$this->json['text'] = join(", ", $validation->errors('validation/object_form')) ;
			return;
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

				//высылаем код в смс
				$sms_count = ORM::factory('Sms')->cnt_by_phone($contact->contact_clear, $session_id);
				if ($sms_count == 0) {
					$sms = Sms::send($contact->contact_clear, 'Код проверки телефона: '.$code, $session_id, 'sms.from');
				} elseif ($sms_count == 1) {
					$sms = Sms::send($contact->contact_clear, 'Код проверки телефона: '.$code, $session_id, 'sms.from_reserve');
				} else {
					$this->json["code"] = 400;
					$this->json["text"] = "Возникли проблемы с доставкой для Вас кода, обратитесь, пожалуйста, в <a href='http://http://feedback.yarmarka.biz/'>службу техподдержки</a>";
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
				$msg = View::factory('emails/contact_verification_code', 
					array('contact' => $contact->contact, 'code' => $code))
					->render();
				$subj 	= 'Подтверждение email на “Ярмарка-онлайн”';
				Email::send($contact->contact, Kohana::$config->load('email.default_from'), $subj, $msg);
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
				$contact->verified_user_id = $current_user->id;
			}

			// ставим как отмодерированный
			$contact->moderate = 1;
			$contact->save();

			return;
		}

		$this->json["code"] = 400;
		$this->json['text'] = "Не правильный код";
	}
}
