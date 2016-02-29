<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Object extends Controller_Rest {


	public function action_show_contacts() {
			$twig = Twig::factory('detail/contacts/mini');
			$obj_id = (int) $this->post->id;
			$captcha = $this->post->captcha;
		
			$object = ORM::factory('Object', $obj_id);
			if (!$object->loaded() or $object->is_published == 0) {
				$this->response->body("");
				return;
			}

			$twig->error = NULL;
			$twig->captcha = NULL;
			
			$REMOTE_ADDR = URL::SERVER("REMOTE_ADDR");
			$token = "show_contacts:{$REMOTE_ADDR}";
			
			$shows_counter = (Cache::instance("memcache")->get($token)) ? Cache::instance("memcache")->get($token) : 0;

			$shows_counter = $shows_counter + 1;
			Cache::instance("memcache")->set($token, $shows_counter, Date::MINUTE);

			if ($shows_counter > 1)
			{
				$twig->code = 300;
				$twig->set_filename("block/captcha/contacts");
				$twig->captcha = Captcha::instance()->render();
				if (isset($captcha)) {
					$validation = Validation::factory(array("captcha" => $captcha))
							->rule('captcha', 'not_empty', array(':value', ""))
							->rule('captcha', 'captcha', array(':value', ""));
					if ( !$validation->check())
					{
						$twig->error = "Не правильный код";
						$twig->code = 400;
					} else {
						$result = $this->show_contacts($object);
						foreach ($result as $key => $item) {
							$twig->{$key} = $item;
						}
						$twig->set_filename('detail/contacts/mini');
						Cache::instance("memcache")->delete($token);
					}
				}
			} else {
				$result = $this->show_contacts($object);
				foreach ($result as $key => $item) {
					$twig->{$key} = $item;
				}
				$twig->set_filename('detail/contacts/mini');
			}

			$this->json["code"] = $twig->code;
			$this->json["error"] = $twig->error;
			$this->json["result"] = (string) $twig;
	}

	public function action_write_to_author() {

		/* validate request fields */
		$require = array( 'object_id', 'message' );
		foreach($require as $key) {
			if (!property_exists($this->post, $key) || empty($this->post->{$key})) {
				throw new Exception($key);
			}
		}

		/* check object */
		$object = ORM::factory('Object')
			->where('id', '=', $this->post->object_id)
			->find();

		if (!$object->loaded()) {
			throw new Exception('Bad object_id');
		}
		
		/* get author */
		$author = ORM::factory('User')
			->where('id', '=', $object->author)
			->find();
		if (!$author->loaded()) {
			throw new Exception('No author');
		}

		/* get current user */
		$user = Auth::instance()->get_user();

		if (!$user) {
			throw new Exception('Unauthorized');
		}

		/* prepare message text */
		$message = 'Вам было отправлено сообщение по объявлению: ' . $object->get_full_url() . "\r\n";
		$message .= 'Текст сообщения:' . "\r\n";
		$message .= '------------------------------------' . "\r\n";
		$message .= htmlspecialchars($this->post->message) . "\r\n";
		$message .= '------------------------------------' . "\r\n";
		$message .= 'Email отправителя: ' . $user->email;

		/* prepare subject */
		$subject = 'У Вас сообщение на «Ярмарка-онлайн»';

		Email::send( $author->email, Kohana::$config->load('email.default_from'), $subject, $message, false);
	}

	public function action_callback() {

		$oc = ORM::factory('Object_Callback');

		$oc->reason = $this->post->reason;
		$ids = $this->post->ids;

		try {
			foreach ($ids as $id) {
				$oc->object_id = $id;
				$oc->save();
			}
		} catch(ORM_Validation_Exception $e)
		{
			$this->json["code"] = 400;
			return;
		}

		$this->json["code"] = 200;
	}

	public function action_backcall() {

		$cr = ORM::factory('Callback_Request');

		$cr->key = $this->post->key;
		$cr->fio = $this->post->name;
		$cr->phone = $this->post->phone;
		$cr->object_id = $this->post->object_id;
		$cr->comment = $this->post->comment;

		try {
			$cr->save();
		} catch(ORM_Validation_Exception $e)
		{
			$this->json["code"] = 400;
			return;
		}

		$this->json["code"] = 200;
	}


	public function action_group_publishun() {

		$ids = $this->post->ids;
		$publish = $this->post->to_publish;
		$all = $this->post->all;
		$user = Auth::instance()->get_user();

		if (!$ids OR !count($ids) OR !$user) {
			throw new HTTP_Exception_404;
		}

		$query = ORM::factory('Object')->where("author","=",$user->id);

		if (!$all){
			$query = $query->where("id","IN", $ids);
		}
						
		$objects = $query->find_all();
		$ids_to_action = array();
		$errors = 0;
		foreach ($objects as $object) {
			$info = NULL;
			if ($publish) {
				$info = Object::canEdit(Array("object_id" => $object->id, "rubricid" => $object->category, "city_id" => $object->city_id));
				if ($info["code"] == "error")
				{
					$this->json['text'] = $info["errors"];
					$errors++;
					continue;
				}
				$ids_to_action[] = $object->id;
			} else {
				$ids_to_action[] = $object->id;
			}
		}

		if ($errors > 0) {
			$this->json['code'] = 400;
			return;
		}

		DB::update("object")
			->where("id","IN", $ids_to_action)
			->set(array("is_published" => ($publish) ? 1: 0))
			->execute();

		$this->json['code'] = 200;
		$this->json['affected'] = $ids_to_action;
		$this->json['errors'] = $errors;
	}

	private function show_contacts($object) {
		$result = array();
		
		$contacts = $object->get_contacts();
		
		foreach ($contacts as $contact)
		{	
			$contact->increase_visits($object->id);
		}

		$object->increase_stat_contacts_show();

		//get contacts from compiled obejct prior to 
		// http://yarmarka.myjetbrains.com/youtrack/issue/yarmarka-363
		$compiledEntry = ORM::factory('Object_Compiled')
			->where('object_id', '=', $object->id)
			->find();
		$decompiledData = unserialize($compiledEntry->compiled);
		$contacts = array();
		foreach($decompiledData['contacts'] as $contact) {
			if ($contact['type'] == 5) {
				continue;
			}
			$contacts []= array( 'contact' => $contact['value'] );
		}
		
		$result["code"] = 200;
		$result["captcha"] = NULL;
		$result["contacts"] = $contacts;

		return $result;
	}

	public function action_moderate_reasons() {

		$this->json["result"] = ORM::factory('Object_Reason')->getprepared_all();

		$this->json["code"] = 200;
	}

	public function action_moderate() {

		$user = Auth::instance()->get_user();
		$id = $this->post->object_id;
		$type = $this->post->type;
		$comment = $this->post->comment;
		$send_mail = $this->post->send_email;

		if (!$type OR !$id OR !Acl::check("object.moderate")) {
			throw new HTTP_Exception_404;
		}

		$object = ORM::factory('Object', $id);

		if (!$object->loaded()) {
			throw new HTTP_Exception_404;
		}

		$auhor = ORM::factory('User', $object->author);

		if ($type == "block_edit") {
			$object->moderate_ban_for_edit();
		} else if ($type == "block_object") {
			$object->moderate_ban();
		}  else if ($type == "block_full" AND $auhor->loaded()) {
			$auhor->ban($comment);
			//$object->moderate_full_ban();
		}

		ORM::factory('User_Messages')->add_msg_to_object($id, $comment);

		if ($send_mail AND $auhor->loaded() AND $auhor->email)
		{
			$msg = View::factory('emails/manage_object', 
				array(
					'UserName' => $auhor->fullname ? $auhor->fullname : $auhor->login,
					'actions' => array(
						$comment . ' ('.HTML::anchor($object->get_url(), $object->title).')',
					),
				)
			)->render();
			Email::send(trim($auhor->email), Kohana::$config->load('email.default_from'), "Сообщение от модератора сайта", $msg);
		}

		$this->json["code"] = 200;
	}

	public function action_main_page_news() {
		$page = $this->post->page;
		$perPage = $this->post->perPage;
		$category = property_exists($this->post, 'category') ? $this->post->category : NULL;

		$this->json['page'] = $page;
		$this->json['perPage'] = $perPage;

		if (!property_exists($this->post, 'category')) {
			$this->json['code'] = 400;
			$this->json['error'] = 'category';
			return;
		}

		$categories = Controller_Block_News::get_categories($this->post->category);
		$newsGroups = Controller_Block_News::get_items(
			$categories, 
			NULL,
			true,
			$page,
			$perPage,
			false);

		$this->json['result'] = $newsGroups;
	}

}