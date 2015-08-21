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

		private function show_contacts($object) {
			$result = array();
			
			$contacts = $object->get_contacts();
			
			foreach ($contacts as $contact)
			{	
				$contact->increase_visits($object->id);
			}

			$object->increase_stat_contacts_show();

			
			$result["code"] = 200;
			$result["captcha"] = NULL;
			$result["contacts"] = $contacts;

			return $result;
		}

}