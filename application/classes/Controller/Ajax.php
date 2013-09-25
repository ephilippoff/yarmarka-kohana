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
				->label('org_name', 'Название компании');
		}

		if ( ! $validation->check())
		{
			$this->json['code']	= 401;
			$this->json['errors'] = join("<br />", $validation->errors('validation'));
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
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		$user->delete_contact($this->request->post('contact_id'));
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
			$is_phone = Model_Contact_Type::is_phone($contact_type_id);
			// проверяем телефоны по contact_clear
			if ($is_phone)
			{
				$exists_contact = ORM::factory('Contact')
					->where('contact_type_id', 'IN', array(1,2))
					->where('contact_clear', '=', $contact_clear)
					->where_user_id($user->id)
					->find();
			}
			else // другие типы контактов
			{
				$exists_contact = ORM::factory('Contact')
					->where('contact_type_id', '=', $contact_type_id)
					->where('contact', '=', $contact)
					->where_user_id($user->id)
					->find();
			}

			if ($exists_contact->loaded())
			{
				$this->json['code']		= 401;
				if ($is_phone)
				{
					$this->json['error'] = Request::factory('block/not_unique_contact_msg/'.$contact_clear)->execute()->body();
				}
				else
				{
					$this->json['error'] = 'Такой контакт уже есть';
				}
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

		$validate_object = $ad->city_id > 0 AND ! empty($ad->title) AND ! empty($ad->user_text) AND $ad->contacts->count_all() > 0;

		if ( ! $validate_object)
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

		if ($object->in_archive())
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
		
		if ($object->is_bad == 1 AND $object->in_archive())
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
			
			$object->prolong($date_expiration, TRUE, TRUE);
			
			$this->json['date_expiration'] = date('d.m.y', strtotime($date_expiration));
			$this->json['code'] = 300;
		}
	}

	public function action_pub_toggle()
	{
		$object = ORM::factory('Object', intval($this->request->param('id')));
		if ( ! $object->loaded() 
				OR ! $object->category_obj->loaded() 
				OR ! Auth::instance()->get_user() 
				OR Auth::instance()->get_user()->id != $object->author)
		{
			throw new HTTP_Exception_404;
		}

		$count_published_in_category = ORM::factory('Object')
			->where('category', '=', $object->category)
			->where('author', '=', $object->author)
			->where('is_published', '=', '1')
			->count_all();

		if ($object->is_published == 0
				AND Auth::instance()->get_user()->org_type == 1 
				AND $object->category_obj->max_count_for_user 
				AND $count_published_in_category >= $object->category_obj->max_count_for_user)
		{
			$json['code'] = 400;
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
				->where('author', '=', Auth::instance()->get_user()->id)
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

		$lon = trim($this->request->post('lon'));
		$lat = trim($this->request->post('lat'));

		$address 		= trim($this->request->post('address'));

		$kladr_city = Model::factory('Kladr')->get_city_by_id($this->request->post('city_kladr_id'));
		$kladr_address = Model::factory('Kladr')->get_address_by_id($this->request->post('address_kladr_id'));

		$location = ORM::factory('Location', array('lon' => $lon, 'lat' => $lat));
		try
		{
			if ( ! $location->loaded())
			{
				$location->lon = $lon;
				$location->lat = $lat;
				$location->address = $address;
				if ($kladr_city)
				{
					$location->region = $kladr_city->region;
					$location->city = $kladr_city->city;
				}
				if ($kladr_address)
				{
					$housenum = $kladr_address->housenum.($kladr_address->buidnum ? ', '.$kladr_address->buildnum : '');

					$location->housenum = $housenum;
					$location->street = $kladr_address->address;
				}
				$location->save();
			}

			$user->location = $location;
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

		if ($this->json['code'] === 200)
		{
			$link_request = ORM::factory('User_Link_Request');
			$link_request->user_id = Auth::instance()->get_user()->id;
			$link_request->linked_user_id = $user->id;
			$link_request->save();
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

	public function after()
	{
		parent::after();
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}
}
