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

			if (isset($data['city_id']))
			{
				$data['city_title'] = $user->user_city->loaded() ? $user->user_city->title : '';
			}

			if (isset($data['login']))
			{
				$data['user_page'] = CI::site('users/'.$user->login);
			}
		}

		$this->json['data'] = $data;
	}

	public function action_upload_user_avatar()
	{
		if ( ! $user = Auth::instance()->get_user())
		{
			throw new HTTP_Exception_404;
		}

		try
		{
			$user->filename = Uploads::save($_FILES['avatar_input']);
			$this->json['filename'] = Uploads::get_file_path($user->filename, '125x83');
			$user->save();
		}
		catch (Exception $e)
		{
			$this->json['error']	= $e->getMessage();
			$this->json['code']		= $e->getCode();
		}
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

		$this->json['cities'] = $cities->find_all()
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

		if ($contact AND $contact_type_id)
		{
			$exists_contact = ORM::factory('Object_Contact')
				->where('contact_type_id', '=', $contact_type_id)
				->where('contact', '=', $contact)
				->where('user_id', '=', $user->id)
				->find();
			if ($exists_contact->loaded())
			{
				$this->json['code']		= 401;
				$this->json['error']	= 'Такой контакт уже есть';
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
			// @todo надо дописать метод save_all для ORM
			DB::update('object')->value('active', 0)
				->value('is_published', 0)
				->where('author', '=', Auth::instance()->get_user()->id)
				->where('id', 'IN', $ids)
				->execute();
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

	public function after()
	{
		parent::after();
		if ( ! $this->response->body())
		{
			$this->response->body(json_encode($this->json));
		}
	}
}
