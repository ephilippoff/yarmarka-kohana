<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ajax controller
 * 
 * @uses Controller
 * @uses _Template
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Controller_Ajax extends Controller_Template
{
	protected $json = array();

	public function before()
	{
		// only ajax request is allowed
		if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
		// disable global layout for this controller
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;
		parent::before();

		$this->json['code'] = 200; // code by default
	}

	public function action_save_user_data()
	{
		$data = $this->request->post();
		$user = Auth::instance()->get_user();

		if ( ! $data OR ! $user)
		{
			throw new HTTP_Exception_404;
		}

		// about field xss validation
		if (isset($data['about']))
		{
			$data['about'] = preg_replace("/&#?[a-z0-9]+;/i","", strip_tags($data['about'],'<em><strong><ol><ul><li><span><br><p>'));
		}

		$validation = Validation::factory($data);
		if (isset($data['org_name']))
		{
			$validation->rule('org_name', 'not_empty')
					->rule('org_name', 'max_length', array(':value', '150'));
			
			$validation->label('org_name', 'Название компании');
		}
		
		if (isset($data['login']))
		{
			$validation->rule('login', 'not_empty')
					->rule('login', 'alpha_numeric')
					->rule('login', 'max_length', array(':value', '10'));
			
			$validation->label('login', 'Поле');						
		}		

		if ( ! $validation->check())
		{
			$this->json['code']	= 401;
			$this->json['errors'] = join("<br />", $validation->errors('validation/profile'));
		}
		else
		{
			if (isset($data['city_kladr_id']))
			{
				$city = ORM::factory('City')
					->where('kladr_id', '=', $data['city_kladr_id'])
					->find();

				if ($city->loaded())
				{
					$user->user_city = $city;
					$user->save();
				}
				else
				{
					// @todo искать город в КЛАДР и добавлять в нашу базу
					// $kladr_city = Model::factory('Kladr')->get_city_by_id($data['city_kladr_id']);
					// $new_city = ORM::factory();
				}

				unset($data['city_kladr_id']);
			}

			try
			{
				// update userdata
				$user->values($data)
					->update();
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->json['code'] = 402;
				$this->json['errors'] = join('<br />', $e->errors('validation'));
			}

			// return values back to client
			foreach ($data as $key => $value)
			{
				$data[$key] = $user->$key;
			}

			if (isset($city) AND $city->loaded())
			{
				$data['city_title'] = $city->title;
			}

			if (isset($data['login']))
			{
				$data['user_page'] = CI::site('users/'.$user->login);
			}
		}

		$this->json['data'] = $data;
	}

	public function action_delete_user_avatar()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		if ($user->filename)
		{
			Uploads::delete($user->filename);
			$user->filename = NULL;
			$user->save();
		}
	}

	public function action_get_cities_by_region_id()
	{
		if ( ! $region = ORM::factory('Region', $this->request->post('region_id')))
		{
			throw new HTTP_Exception_404;
		}

		 $cities = $region->cities
			->order_by('title');

		 if ($this->request->post('only_visible'))
		 {
			$cities->where('is_visible', '=', 1);
		 }

		$this->json['cities'] = $cities->order_by('title')
			->find_all()
			->as_array('id', 'title');
	}

	public function action_delete_user_contact()
	{
		$contact 	= ORM::factory('Contact', $this->request->post('contact_id'));
		$user 		= Auth::instance()->get_user();
		if ( ! $user OR ! $contact->loaded() OR $contact->verified_user_id !== $user->id)
		{
			throw new HTTP_Exception_404;
		}

		// снимаем все объявления с контактом
		foreach ($contact->objects->find_all() as $object)
		{
			$object->is_published = 0;
			$object->save();
		}
		// убираем привязку контакта к объявлениям
		$contact->remove('objects');
		// отвязываем контакт от пользователя
		$user->remove('contacts', $contact);
		$contact->verified_user_id = NULL;
		$contact->save();
	}

	public function action_unlink_user_contact()
	{
		$contact 	= ORM::factory('Contact', $this->request->post('contact_id'));
		$user 		= Auth::instance()->get_user();
		if ( ! $user OR ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// отвязываем контакт от пользователя
		$user->remove('contacts', $contact);
	}

	public function action_link_objects_by_contact()
	{
		$contact 	= ORM::factory('Contact', $this->request->param('id'));
		$user 		= Auth::instance()->get_user();
		if ( ! $user OR ! $contact->loaded() 
			OR 
			$contact->verified_user_id !== $user->id 
			OR 
			$contact->moderate == 0
		)
		{
			throw new HTTP_Exception_404;
		}

		$this->json['affected_rows'] = 0;
		foreach ($contact->objects->find_all() as $object)
		{
			if ($object->author != $user->id)
			{
				$object->author = $object->author_company_id = $user->id;
				$object->save();

				$this->json['affected_rows']++;
			}
		}
	}

	public function action_add_user_contact()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$contact_type_id	= intval($this->request->post('contact_type_id'));
		$contact			= trim($this->request->post('contact'));
		$contact_clear		= Text::clear_phone_number($contact);

		if ($contact AND $contact_type_id)
		{
			$exists_contact = ORM::factory('Contact')
				->by_contact_and_type($contact, $contact_type_id)
				->find();

			if ($exists_contact->loaded() AND $exists_contact->verified_user_id === $user->id)
			{
				$this->json['code']		= 401;
				$this->json['error'] 	= 'Этот контакт уже привязан к вашей учетной записи';
			}
			else
			{
				$contact = $user->add_contact($contact_type_id, $contact);
			}
		}
		else
		{
			$this->json['code'] = 400;
		}
	}

	public function action_user_profile_contacts()
	{
		$this->response->body(Request::factory('block/user_profile_contacts')->execute());
	}

	public function action_verified_profile_contacts()
	{
		$this->response->body(Request::factory('block/verified_profile_contacts')->execute());
	}

	public function action_remove_from_user_favorites()
	{
		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $user = Auth::instance()->get_user() OR ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ( !$object->remove_from_favorites())
		{
			$this->json['code'] = 500;
		}
	}

	public function action_unsubscribe()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$subscription = ORM::factory('Subscription')->where('id', '=', intval($this->request->param('id')))
			->where('user_id', '=', $user->id)
			->find();

		if ( ! $subscription->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$subscription->delete();
	}

	public function action_change_subscription_period()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$subscription = $user->subscriptions
			->where('id', '=', intval($this->request->param('id')))
			->find();
		$period = (int) $this->request->post('period');

		if ( ! $subscription->loaded() OR ! $period)
		{
			$this->json['code'] = 500;
		}
		else
		{
			$subscription->period = $period;
			$subscription->save();
		}
	}

	public function action_service_up()
	{
		$ad = ORM::factory('Object', intval($this->request->param('id')));
		if ( ! $ad->loaded() OR ! Auth::instance()->get_user() OR $ad->author != Auth::instance()->get_user()->id)
		{
			throw new HTTP_Exception_404;
		}

		$this->json['edit_link'] = CI::site('user/edit_ad/'.$ad->id.'#contacts');

		if ( ! $ad->is_valid())
		{
			$this->json['code'] = 500;
		}
		elseif ($ad->get_service_up_timestamp() > time())
		{
			$this->json['code'] = 300;
			$this->json['date_service_up_available'] = date("d.m Y в H:i", $ad->get_service_up_timestamp());
		}
		else
		{
			$ad->up();
			$this->json['date_service_up_available'] = date("d.m Y в H:i", $ad->get_service_up_timestamp());
		}
	}

	public function action_prolong_object()
	{
		$object = ORM::factory('Object', intval($this->request->param('id')));
		$lifetime = $this->request->post('lifetime');
		
		if ( ! $object->loaded() 
				OR ! $lifetime 
				OR ! Auth::instance()->get_user() 
				OR $object->author != Auth::instance()->get_user()->id)
		{
			throw new HTTP_Exception_404;
		}

		if ($object->is_bad == 0 AND $object->in_archive AND $object->is_valid())
		{
			$date_expiration = null;

			switch ($lifetime) {
				case "1m":
					$date_expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "+1 month"));
					break;
				case "2m":
					$date_expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "+2 month"));
					break;
				case "3m":
					$date_expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "+3 month"));
					break;
				default:
					$date_expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "+14 days"));
					break;
			}
			
			$object->prolong($date_expiration);
			
			$this->json['date_expiration'] = date('d.m.y', strtotime($date_expiration));
			$this->json['code'] = 200;
		}
		else
		{
			$this->json['code'] = 300;
			$this->json['edit_link'] = CI::site('user/edit_ad/'.$object->id.'#contacts');
		}
	}

	public function action_pub_toggle()
	{
		$object = ORM::factory('Object', intval($this->request->param('id')));
		if ( ! $object->loaded() 
				OR ! $object->category_obj->loaded() 
				OR ! Auth::instance()->get_user() 
				OR 
				(	Auth::instance()->get_user()->id != $object->author
					AND 
					Auth::instance()->get_user()->id != $object->author_company_id
				)
			)
		{
			throw new HTTP_Exception_404;
		}

		$count_published_in_category = ORM::factory('Object')
			->where('category', '=', $object->category)
			->where('author', '=', $object->author)
			->where('is_published', '=', '1')
			->count_all();

		if (
			$object->is_published == 0
			AND Auth::instance()->get_user()->org_type == 1 
			AND $object->category_obj->max_count_for_user 
			AND $count_published_in_category >= $object->category_obj->max_count_for_user
		)
		{
			$this->json['code'] = 400;
		}
		elseif ( ! $object->is_published() AND ! $object->is_valid())
		{
			$this->json['code'] = 401;
			$this->json['edit_link'] = CI::site('user/edit_ad/'.$object->id.'#contacts');
		}
		else
		{
			$object->toggle_published();
			$this->json['code'] = 200;
			$this->json['is_published'] = $object->is_published;
		}
	}

	public function action_delete_ads()
	{
		if ( ! Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$ids = $this->request->post('to_del');

		if (is_array($ids) AND $ids)
		{
			ORM::factory('Object')
				->where_open()
					->where('author', '=', Auth::instance()->get_user()->id)
					->or_where('author_company_id', '=', Auth::instance()->get_user()->id)
				->where_close()
				->where('id', 'IN', $ids)
				->set('active', 0)
				->update_all();
		}
		else
		{
			$this->json['code'] = 400;
		}
	}

	public function action_load_moderator_comments()
	{
		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$comments = $object->user_messages->from_moderator()
			->order_by('createdOn')
			->cached()
			->find_all();
		$this->json['html'] = View::factory('user/moderator_comments')
			->set('comments', $comments)
			->render();
	}

	public function action_delete_newspapers()
	{
		if ( ! Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$ids = $this->request->post('to_del');

		if (is_array($ids) AND $ids)
		{
			DB::delete('service_outputs')
				->where('user_id', '=', Auth::instance()->get_user()->id)
				->where('id', 'IN', $ids)
				->execute();
		}
		else
		{
			$this->json['code'] = 400;
		}
	}

	public function action_transliterate_str()
	{
		$str = $this->request->post('str');

		$this->json['str'] = Url::title($str, '-', TRUE);
	}

	public function action_get_full_text()
	{
		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->json['text'] = $object->user_text;
	}

	public function action_save_userpage_image()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			throw new HTTP_Exception_404;
		}

		$filepath = '/images/userpage/'.$this->request->query('filename');
		if (file_exists(DOCROOT.$filepath))
		{
			$user->userpage_banner = $filepath;
			$user->save();
		}

		$this->json['filepath'] = URL::site($filepath);
	}

	public function action_delete_userpage_image()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			throw new HTTP_Exception_404;
		}

		$user->userpage_banner = NULL;
		$user->save();
	}

	public function action_save_user_location()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$user->location = Location::add_location_by_post_params();
			$user->save();
		}
		catch (Exception $e)
		{
			$this->json['code'] = $e->getCode();
			$this->json['message'] = $e->getMessage();
		}
	}

	public function action_kladr_city_autocomplete()
	{
		$this->json = array();
		$term = trim($this->request->query('term'));

		$results = Model::factory('Kladr')->get_cities($term);
		foreach ($results as $row) 
		{
			$data = array(
				'id' 	=> $row->id,
				'value'	=> $row->city,
				'label'	=> '<span style="font-size:11px">'.$row->region.'</span>, '.Text::highlight_word($term, $row->city),
				'city'	=> $row->city,
				'region'=> $row->region,
			);
			$this->json[] = $data;
		}
	}

	public function action_kladr_address_autocomplete()
	{
		$this->json = array();

		$term 				= trim($this->request->query('term'));
		$city_id 			= trim($this->request->query('parent_id'));
		$housenum_required 	= (bool) $this->request->query('address_required');

		$results = Model::factory('Kladr')->get_address($term, $city_id, $housenum_required);
		foreach ($results as $row) 
		{
			$data = array(
				'id' 		=> $row->id,
				'value'		=> $row->address.', '.$row->housenum.($row->buildnum ? ', '.$row->buildnum : ''),
				'label'		=> Text::highlight_word($term, $row->address.', '.$row->housenum.($row->buildnum ? ', '.$row->buildnum : '')),
				'housenum'	=> $row->housenum,
				'buildnum'	=> $row->buildnum,
				'address'	=> $row->address,
				'aolevel'	=> $row->aolevel,
			);
			$this->json[] = $data;
		}
	}

	public function action_link_user()
	{
		$user = ORM::factory('User');
		$login = $this->request->param('login');
		$user =	$user->where($user->unique_key($login), '=', $login)
			->find();

		if ( ! Auth::instance()->get_user())
		{
			$this->json['code'] = 501;
			$this->json['error'] = 'Ошибка при авторизации пользователя';
		} 
		elseif ( ! $user->loaded())
		{
			$this->json['code'] = 502;
			$this->json['error'] = 'Пользователь с таким логином не найден';
		}
/*		elseif ($user->org_type != 2)
		{
			$this->json['code'] = 503;
			$this->json['error'] = 'Вы можете отправить запрос на привязку только пользователю с типом компания';
		}
*/		elseif ($user->id === Auth::instance()->get_user()->id)
		{
			$this->json['code'] = 504;
			$this->json['error'] = 'Это ваш аккаунт';
		}
		elseif ($user->linked_to->loaded())
		{
			$this->json['code'] = 505;
			$this->json['error'] = $user->linked_to->id == Auth::instance()->get_user()->id
				? 'Пользователь уже является сотрудником Вашей компании'
				: 'Пользователь уже является сотрудником другой компании';
		}

		if ($this->json['code'] === 200)
		{
			$link_request = ORM::factory('User_Link_Request')
				->where('user_id', '=', Auth::instance()->get_user()->id)
				->where('linked_user_id', '=', $user->id)
				->find();
			if ( ! $link_request->loaded())
			{
				$link_request->user_id = Auth::instance()->get_user()->id;
				$link_request->linked_user_id = $user->id;
				$link_request->save();
			}
		}
	}

	public function action_approve_user_link()
	{
		$link = ORM::factory('User_Link_Request', $this->request->param('id'));
		if ( ! $link->loaded())
		{
			$this->json['code'] = 500;
		}
		else
		{
			$user = $link->linked_user;
			$user->linked_to_user = $link->user;
			$user->save();

			$link->delete();
		}
	}

	public function action_decline_user_link()
	{
		$link = ORM::factory('User_Link_Request', $this->request->param('id'));
		if ( ! $link->loaded())
		{
			$this->json['code'] = 500;
		}
		else
		{
			$link->delete();
		}
	}

	public function action_remove_link()
	{
		$current_user = Auth::instance()->get_user();
		if ($this->request->param('id'))
		{
			$user = ORM::factory('User', $this->request->param('id'));
			// проверяем что открепляет пользователь данной компании
			if ( ! $user->linked_to_user == $current_user->id)
			{
				$user = NULL;
			}
		}
		else
		{
			$user = $current_user;
		}

		if ( ! $user OR ! $user->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$user->linked_to = DB::expr('NULL');
		$user->save();
	}

	public function action_delete_link()
	{
		$link = ORM::factory('User_Link_Request', $this->request->param('id'));
		if ( ! $link->loaded() OR $link->user_id != Auth::instance()->get_user()->id)
		{
			throw new HTTP_Exception_404;
		}

		$link->delete();
	}

	public function action_set_as_main_email()
	{
		$contact 	= ORM::factory('Contact', $this->request->param('id'));
		$user 		= Auth::instance()->get_user();

		if ( ! $user OR ! $contact->loaded() OR $contact->verified_user_id !== $user->id OR $contact->contact_type_id !== Model_Contact_Type::EMAIL)
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$user->email = $contact->contact_clear;
			$user->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->json['code'] = 500;
			$this->json['errors'] = $e->errors();
		}

		$this->json['email'] = $user->email;
	}

	public function action_check_contact()
	{
		$session_id 		= Arr::get($_POST, 'session_id', session_id());
		$current_user 		= Auth::instance()->get_user();
		$contact 			= trim($this->request->post('contact'));
		$contact_type_id 	= intval($this->request->post('contact_type_id'));

		$real_contact_type_id = $contact_type_id;

		// clear contact phone
		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$contact = Text::clear_phone_number($contact);
			$real_contact_type_id = Model_Contact_Type::detect_contact_type($contact);
		}

		if ( ! $contact)
		{
			$this->json['code'] = 500;
		}
		elseif ($real_contact_type_id != $contact_type_id)
		{
			$this->json['code'] = 401; // wrong contact type id
		}
		else
		{
			// lookig for contact
			$exists_contact = ORM::factory('Contact')->where('contact_clear', '=', $contact)->find();
			if ($exists_contact->loaded())
			{
				if ($current_user AND (int) $exists_contact->verified_user_id === (int) $current_user->id)
				{
					// contact already belongs to user
					$this->json['code'] = 201;
				}
				elseif ($exists_contact->is_verified($session_id))
				{
					// contact verified for current session
					$this->json['code'] = 201;
				}
				elseif ($exists_contact->verified_user_id AND $exists_contact->contact_type_id == 2)
				{
					// it is home phone and belongs to other user
					$this->json['code'] = 400;
				}
				elseif ($exists_contact->is_blocked())
				{
					$this->json['code'] = 305;
				}
			}
		}
	}

	public function action_verify_home_phone()
	{
		$session_id 	= Arr::get($_POST, 'session_id', session_id());
		$contact_clear 	= Text::clear_phone_number($this->request->post('contact'));
		$contact 		= ORM::factory('Contact')->where('contact_clear', '=', $contact_clear)->find();
		$link_to_user 	= (bool) $this->request->post('link_to_user');

		if ($contact->loaded() AND $contact->verified_user_id)
		{
			$this->json['code'] = 400;
		}
		elseif ($contact->loaded())
		{
			$contact->verify_for_session($session_id);

			if ($link_to_user AND Auth::instance()->get_user())
			{
				$contact->verified_user = Auth::instance()->get_user();
			}

			$contact->moderate = 0;
			$contact->save();
		}
		elseif ( ! $contact->loaded())
		{
			$contact = ORM::factory('Contact')
				->values(array('contact' => $contact_clear, 'contact_type_id' => Model_Contact_Type::PHONE))
				->create();

			$contact->verify_for_session($session_id);
		}
	}

	public function action_check_contact_code()
	{
		$session_id 	= Arr::get($_POST, 'session_id', session_id());
		$contact 		= ORM::factory('Contact', $this->request->param('id'));
		$link_to_user 	= (bool) $this->request->post('link_to_user');

		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$code = trim($this->request->post('code'));
		if (($code) AND ($contact->verification_code) AND ($code === trim($contact->verification_code)))
		{
			$this->json['code'] = 200;

			// верифицируем контакт
			$contact->verify_for_session($session_id);

			if ($link_to_user AND Auth::instance()->get_user())
			{
				$contact->verified_user = Auth::instance()->get_user();
			}

			// ставим как отмодерированный
			$contact->moderate = 1;
			$contact->save();
		}
		else
		{
			$this->json['code'] = 500;
		}
	}

	public function action_sent_verification_code()
	{
		$session_id 		= Arr::get($_POST, 'session_id', session_id());
		$current_user 		= Auth::instance()->get_user();
		$contact_type_id 	= (int) $this->request->post('contact_type_id');
		$real_contact_type_id = $contact_type_id;

		$force = $this->json['force'] = (bool) $this->request->post('force');
		switch ($contact_type_id) {
			case 1:
				$contact = trim($this->request->post('phone'));
				break;
			case 5:
				$contact = trim($this->request->post('email'));
				break;
			
			default:
				$contact = $this->request->post('contact');
				break;
		}
		$code 				= NULL;
		$contact_id 		= NULL;

		if (Model_Contact_Type::is_phone($contact_type_id))
		{
			$contact_clear = Text::clear_phone_number($contact);
			$real_contact_type_id = Model_Contact_Type::detect_contact_type($contact_clear);
		}
		else
		{
			$contact_clear = trim($contact);
		}

		if ( ! $contact)
		{
			throw new HTTP_Exception_404;
		}

		if ($real_contact_type_id != $contact_type_id)
		{
			$this->json['code'] = 401;
			return;
		}

		// lookig for contact
		$exists_contact = ORM::factory('Contact')->where('contact_clear', '=', $contact_clear)->find();
		if ($exists_contact->loaded())
		{
			$this->json['contact_id'] = $exists_contact->id;
			if ($current_user AND (int) $exists_contact->verified_user_id === (int) $current_user->id)
			{
				$this->json['code'] = 301;
				$this->json['msg'] = 'Контакт уже подтвержден';
			}
			elseif ($exists_contact->verified_user_id)
			{
				$this->json['code'] = 302;
				$this->json['msg'] = 'Контакт принадлежит другому пользователю';
			}
			elseif ($exists_contact->is_blocked())
			{
				$this->json['code'] = 305;
				$this->json['msg'] = 'Контакт в черном списке';
			}
			else
			{
				// контакт существует и еще не подтвержден ни одним пользователем
				$code 		= Text::random('numeric', 5);
			}
		}
		else
		{
			// сохраняем новый контакт
			$code 		= Text::random('numeric', 5);
			$exists_contact = ORM::factory('Contact')
				->values(array('contact' => $contact, 'contact_type_id' => $contact_type_id))
				->create();
		}


		if ($code AND $exists_contact->loaded())
		{
			$this->json['contact_id'] = $exists_contact->id;
			if (ORM::factory('Sms')->cnt_by_phone($exists_contact->contact_clear, $session_id) AND ! $force)
			{
				$this->json['code'] = 303;
				$this->json['msg'] = 'SMS уже отрпавлено на этот номер, смотрите код в телефоне';
			}
			elseif ($contact_type_id == Model_Contact_Type::MOBILE 
				AND ORM::factory('Sms')->cnt_by_session_id($session_id) >= Kohana::$config->load('user.max_sms'))
			{
				$this->json['code'] = 304;
				$this->json['msg'] = 'Вы исчерпали лимит SMS на сегодня';
			}
			else
			{
				$exists_contact->verification_code = $code;
				$exists_contact->save();

				if ($contact_type_id == Model_Contact_Type::MOBILE)
				{
					// высылаем код в смс
					Sms::send($exists_contact->contact_clear, 'Ваш проверочный код '.$code, $session_id);
				}
				else
				{
					$msg = View::factory('emails/contact_verification_code', 
						array('contact' => $exists_contact->contact, 'code' => $code))
						->render();
					$subj 	= 'Подтверждение email на “Ярмарка-онлайн”';
					Email::send($exists_contact->contact, Kohana::$config->load('email.default_from'), $subj, $msg);
				}
			}
		}
	}

	public function action_get_business_types()
	{
		$this->json['business_types'] 	= ORM::factory('Business_Type')->find_all()->as_array('id', 'title');
		if ($user = Auth::instance()->get_user())
		{
			$this->json['selected'] 	= $user->business_types->find_all()->as_array('id', 'title');
		}
	}

	public function action_save_user_business_types()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$types = $this->request->post('types');
		if ($types)
		{
			$user->remove('business_types');
			$user->add('business_types', $types);
		}

		$this->json['business_types'] = $user->business_types->find_all()->as_array('id', 'title');
	}

	public function action_units_for_category()
	{
		if ( ! $category = ORM::factory('Category', $this->request->param('id')))
		{
			throw new HTTP_Exception_404;
		}

		$this->template = View::factory('ajax/units_for_category');
		$this->template->units = ORM::factory('User_Units')
			->by_category($category->id)
			->limit(10)
			->find_all();

		$this->response->body($this->template->render());
	}
	
	public function action_get_unit()
	{
		$id = (int)$this->request->post('id');
		
		$unit = ORM::factory('User_Units')
				->where('id', '=', $id)
				->find();
		
		if (!($unit->loaded()))
			$this->json['code'] = 404;		
		else
		{
			$data = $unit->as_array();
			
			$job_category_id = 36;//TODO: Костыль: Пропись id

			$job_adverts_count = ORM::factory('Object')
					->where('author_company_id', '=', $unit->user->id)
					->where('active', '=', 1)
					->where('is_published', '=', 1)
					->where('category', '=', $job_category_id)
					->where('date_expired', '<=',  DB::expr('CURRENT_TIMESTAMP'))
					->count_all();			

			$data['kac_vacancies_link'] = ( $job_adverts_count > 0 )
					? 
					ORM::factory('Category')->where('id', '=', $job_category_id)->find()->get_url().'?user_id='.$unit->user->id
					: 
					null;			
		
			$data['kac_objects_link'] = ORM::factory('Category')->where('id', '=', 1)->find()->get_url().'?user_id='.$unit->user->id;
			
			$data['web'] = trim($data['web']) ? URL::prep_url(strip_tags($data['web'])) : null;
			
			$data['address'] = $unit->location->address;
					
			$data['userpage'] = Route::get('userpage')->uri(array('login' => $unit->user->login));
			$data['title']		 = htmlspecialchars($data['title']);
			$data['description'] = htmlspecialchars($data['description']);
			$data['contacts']	 = htmlspecialchars($data['contacts']);
			
			$imageArr = Imageci::getSavePaths($data['filename']);
			
			if (isset($imageArr['136x136']) && (strlen($imageArr['136x136']) > 20))
			{	
				$image_path =  trim($imageArr['136x136'], '.');
				$image_file = $_SERVER['DOCUMENT_ROOT'].$image_path; 

				if( is_file($image_file))
					$data['file'] = $image_path;	
				else 
					$data['file'] = null;
				
			}			
			
			$this->json['data'] = $data;
		}
	}	

		public function action_object_upload_file()
		{
			try
			{
				if (empty($_FILES['userfile1']))
			{
				throw new Exception('Не загружен файл');
			}

			$filename = Uploads::make_thumbnail($_FILES['userfile1']);

			$check_image_similarity = Kohana::$config->load('common.check_image_similarity');

			if ($check_image_similarity)
			{
				$similarity = ORM::factory('Object_Attachment')->get_similarity(Uploads::get_full_path($filename));
				if ($similarity > Kohana::$config->load('common.max_image_similarity'))
				{
					throw new Exception('Фотография не уникальная, уже есть активное объявление с такой фотографией либо у вас , либо у другого пользователя');
				}
			}

			$this->json['filename'] = $filename;
			$this->json['filepaths'] = Imageci::getSitePaths($filename);

			$tmp_img = ORM::factory('Tmp_Img');
			$tmp_img->name = $filename;
			$tmp_img->save();
			}
				catch(Exception $e)
			{
				$this->json['error'] = $e->getMessage();
			}
		}

	public function after()
	{
		parent::after();
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}

//------------------------------------------------------------------
	 public function action_ajax_get_hints_by_page()
	 {
	 	$this->json = array();

	 	$controller = trim($this->request->post('controller'));

	 	$hints = Model::factory('Notice')->GetHintsByController($controller);
		
	 	foreach ($hints as $hint)
	 	{
	 		//var_dump($hint);
	 		$data = array(
	 			'identify'	=> $hint->identify,
	 			'left'	=> $hint->left,
	 			'tops'	=> $hint->top,
	 			'width'	=> $hint->width,
	 			'height' => $hint->height,
	 			'html' 	=> View::factory('block/hint')->bind('hint', $hint)->render()
	 		);

	 		$this->json[] = $data;

	 	}
	 }
	 
	 public function action_ajax_get_obj_stat()
	 {
	 	$obj_id = (int)($this->request->post('obj_id'));

		$this->json = array();
		
	 	$stat = Model::factory('Objectstat')
				->where('object_id', '=', $obj_id)
				->and_where('date', '>=', DB::expr("CURRENT_DATE - interval '14 days'"))
				->and_where('date', '<', DB::expr("CURRENT_DATE"))
				->order_by('date', 'asc')
				->find_all();
		
		foreach ($stat as $value) 
		{
			$this->json[] = array('date' => strtotime($value->date.' UTC') * 1000,	'visits' => $value->visits, 'contacts_show_count' => $value->contacts_show_count);
		}	
	 }	 

//	public function  action_get_hints_by_page()
//	{
//		$controllerCharacter = trim($this->request->query('controller_character'));
//		
//		$hint = Model::factory('Notice')->getHintByCChar($controllerCharacter);
//		
//		if (!$hint) return;
//
//		$data = array(
//			'identify' => $hint->identify,
//			'left'	=> $hint->left,
//			'tops'	=> $hint->top,
//			'width'	=> $hint->width,
//			'height' => $hint->height,
//			'html' 	=> View::factory('block/hint')->bind('hint', $hint)->render()
//		);
//
//		$this->json['hint'] = $data;
//	}
//--------------------------------------------------------------
}
