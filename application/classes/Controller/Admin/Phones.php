<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Phones extends Controller_Admin_Template {

	public function action_index()
	{
		$limit  = 50;
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$contacts = ORM::factory('Contact')
			->where('contact_type_id', 'IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE))
			->where('verified_user_id', 'IS', DB::expr('NOT NULL'))
			->where('moderate', '=', 0);

		$clone_to_count = clone $contacts;
		$count_all = $clone_to_count->count_all();

		$contacts->offset($offset)
			->limit($limit)
			->order_by('id', 'desc');

		$this->template->contacts	= $contacts->find_all();
		$this->template->pagination	= Pagination::factory(array(
			'current_page'   => array('source' => 'query_string', 'key' => 'page'),
			'total_items'    => $count_all,
			'items_per_page' => $limit,
			'auto_hide'      => TRUE,
			'view'           => 'pagination/bootstrap',
		))->route_params(array(
			'controller' => 'phones',
			'action'     => 'index',
		));
	}

	public function action_confirm()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$contact->moderate = 1;
		$contact->save();

		$this->response->body(json_encode($json));
	}

	public function action_decline()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$objects = ORM::factory('Object')
			->join('object_contacts')
			->on('object.id', '=', 'object_contacts.object_id')
			->where('contact_id', '=', $contact->id)
			->where('author', '=', $contact->verified_user_id)
			->find_all();


		foreach ($objects as $object)
		{
			$object->is_published = 0;
			$object->save();

			DB::delete('object_contacts')
				->where('object_id', '=', $object->id)
				->where('contact_id', '=', $contact->id)
				->execute();
		}

		$contact->verified_user->delete_contact($contact->id);
		$contact->verified_user_id = DB::expr('NULL');
		$contact->save();

		$this->response->body(json_encode($json));
	}
}

/* End of file Phones.php */
/* Location: ./application/classes/Controller/Admin/Phones.php */