<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Objects extends Controller_Admin_Template {

	protected $module_name = 'object';

	public function action_index()
	{
		set_time_limit(60);
		ini_set('memory_limit', '512M');
		// Kohana::$profiling = TRUE; // @todo

		$limit  = Arr::get($_GET, 'limit', 50);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$objects = ORM::factory('Object')
			->with('user')
			->with('city_obj')
			->with('category_obj')
			->with('location_obj')
			->with_main_photo()
			->with_used_service(41)
			->with_selection()
			->where('source_id', '=', 1)
			->where('active', '=', 1)
			->where('is_union','IS', NULL)
			->where_open()
				->where('type_tr', 'IS', NULL)
				->or_where('type_tr', '=', 1)
			->where_close();

		/**
		 * Filters
		 */
		$filters_enable = TRUE;

		if ($user_id = intval($this->request->query('user_id')))
		{
			$filters_enable = FALSE;
			$this->template->author = ORM::factory('User', $user_id);
			$objects->where('author', '=', $user_id);
		}

		if ($filters_enable AND $email = trim(mb_strtolower($this->request->query('email'))))
		{
			if (is_numeric($email)) // can be email or object id
			{
				$filters_enable = FALSE;
				$objects->where('object.id', '=', $email);
			}
			else
			{
				$objects->where('email', 'LIKE', '%'.$email.'%');
			}
		}

		if ($filters_enable AND $contact = trim(mb_strtolower($this->request->query('contact'))))
		{
			$objects
				->where_open()
					->where('', 'EXISTS', DB::expr('(SELECT oc.id FROM object_contacts as oc 
								JOIN contacts as c ON c.id = oc.contact_id 
								WHERE oc.object_id=object.id AND c.contact LIKE \'%'.$contact.'%\')'))
					->or_where('object.contact', 'LIKE', '%'.$contact.'%')
				->where_close();
		}

		if ($filters_enable AND $date = $this->request->query('date') AND !$contact AND !$email)
		{
			$field = $this->request->query('date_field');
			if ($from_time = strtotime($date['from']))
			{
				$objects->where(DB::expr("date($field)"), '>=', DB::expr("date '".date('Y-m-d', $from_time)."'"));
			}

			if ($to_time = strtotime($date['to']))
			{
				$objects->where(DB::expr("date($field)"), '<=', DB::expr("date '".date('Y-m-d', $to_time)."'"));
			}
		}
		elseif ($filters_enable  AND !$contact AND !$email)
		{
			$objects->where(DB::expr('date(real_date_created)'), '>', DB::expr("date '".date('Y-m-d', strtotime('-3 days'))."'"));
		}

		if ($filters_enable AND $category_id = intval($this->request->query('category_id')))
		{
			$objects->where('object.category', '=', $category_id);
		}

		if ($filters_enable AND '' !== ($moder_state = Arr::get($_GET, 'moder_state', '0'))  AND !$contact AND !$email)
		{
			if ($moder_state == 3)
			{
				$objects->where('', 'EXISTS', DB::expr('(SELECT cmpl.id FROM complaints as cmpl 
					WHERE cmpl.object_id=object.id)'));
			}
			else
			{
				$objects->where('object.moder_state', '=', $moder_state);
			}
		}

		// count all objects
		$clone_to_count = clone $objects;
		$count_all = $clone_to_count->count_all();

		$objects->limit($limit)
			->offset($offset);

		// order
		$sort_by	= trim($this->request->query('sort_by')) ? trim($this->request->query('sort_by')) : 'real_date_created';
		$direction	= trim($this->request->query('direction')) ? trim($this->request->query('direction')) : 'desc';

		$objects->order_by('to_forced_moderation', 'desc');
		$objects->order_by($sort_by, $direction);

		$this->template->sort_by 	= $sort_by;
		$this->template->direction 	= $direction;

		$this->template->objects 	= $objects->find_all();

		$this->template->categories = ORM::factory('Category')
			->order_by('title')
			->find_all()
			->as_array('id', 'title');
		$this->template->limit = $limit;
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'objects',
				'action'     => 'index',
			));
	}

	public function action_ajax_change_moder_state()
	{
		$this->auto_render = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (intval($this->request->post('moder_state')))
		{
			$object->moder_state 	= 1;
			// $object->is_published 	= 1;
			$object->is_bad 		= 0;
		}
		else
		{
			$object->moder_state 	= 0;
		}
		$object->save();
		
		// moderation log
		$m_log = ORM::factory('Object_Moderation_Log');
		$m_log->action_by 	= Auth::instance()->get_user()->id;
		$m_log->user_id 	= $object->author;
		$m_log->description = $object->moder_state ? "Прошло модерацию" : "На модерации" ;
		$m_log->reason 		= "STATUS".$object->moder_state;
		$m_log->object_id 	= $object->id;
		$m_log->save();		
	}

	public function action_ajax_decline()
	{
		$this->use_layout = FALSE;

		$this->decline_form(1);
	}

	public function action_ajax_ban()
	{
		$this->use_layout = FALSE;

		$this->decline_form(2);
	}

	public function action_ajax_delete($value='')
	{
		$this->use_layout = FALSE;

		$this->decline_form(0);
	}

	private function decline_form($is_bad)
	{
		$this->template = View::factory('admin/objects/decline_form');

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object 	= $object;
		$this->template->reasons 	= ORM::factory('Object_Reason')->find_all()->as_array('id', 'full_text');
		$this->template->is_bad 	= $is_bad;
	}

	public function action_decline()
	{
		$this->auto_render = FALSE;
		$json = array('code' => 400);

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$reason = trim($this->request->post('reason'));
		$is_bad = intval($this->request->post('is_bad'));

		if ($is_bad == 1)
		{
			$description = "Заблокировано до исправления по причине : $reason";
		} 
		elseif ($is_bad == 2)
		{
			$description = "Заблокировано окончательно по причине : $reason";
		}
		else
		{
			$description = "Удалено по причине: $reason";
		}

		if ($reason)
		{
			// moderation log
			$m_log = ORM::factory('Object_Moderation_Log');
			$m_log->action_by 	= Auth::instance()->get_user()->id;
			$m_log->user_id 	= $object->author;
			$m_log->description = $description;
			$m_log->reason 		= $reason;
			$m_log->object_id 	= $object->id;
			$m_log->save();

			// msg to user
			ORM::factory('User_Messages')->add_msg_to_object($object->id, $description);

			if ($this->request->post('send_email') AND $object->user->loaded())
			{
				$msg = View::factory('emails/manage_object', 
					array(
						'UserName' => $object->user->fullname ? $object->user->fullname : $object->user->login,
						'actions' => array(
							$description . ' ('.HTML::anchor($object->get_url(), $object->title).')',
						),
					)
				)->render();
				Email::send(trim($object->user->email), Kohana::$config->load('email.default_from'), "Сообщение от модератора сайта", $msg);
			}
						

			if ($is_bad)
			{
				$object->is_published 	= 0;
				$object->is_bad 		= $is_bad;
				$object->moder_state 	= 1;
				$object->save();
			}
			else
			{
				$object->active 		= 0;
				$object->is_published 	= 0;
				$object->moder_state 	= 1;
				$object->save();
			}
			$json['code'] = 200;
		}

		$this->response->body(json_encode($json));
	}

	public function action_object_row()
	{
		$this->use_layout = FALSE;

		$object = ORM::factory('Object')
			->where('object.id', '=', $this->request->param('id'))
			->with('user')
			->with('city_obj')
			->with('category_obj')
			->with_main_photo()
			->find();
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object = $object;
	}

	public function action_edit()
	{
		$this->use_layout = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object = $object;
	}

	public function action_save()
	{
		$this->auto_render = FALSE;
		$json = array('code' => 400);

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$title = $this->request->post('title');
		$user_text = $this->request->post('user_text');

		if ( ! $title OR ! $user_text)
		{
			$json['errors'] = 'Заполните все поля';
		}
		else
		{
			$object->title 		= trim($title);
			$object->user_text 	= trim($user_text);
			$object->save();

			$json['code'] = 200;
		}

		$this->response->body(json_encode($json));
	}

	public function action_complaints()
	{
		$this->use_layout = FALSE;

		$object = ORM::factory('Object', $this->request->param('id'));
		if ( ! $object->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template->object = $object;
	}
}

/* End of file Objects.php */
/* Location: ./application/classes/Controller/Admin/Objects.php */