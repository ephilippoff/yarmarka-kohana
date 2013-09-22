<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Phones extends Controller_Admin_Template {

	public function action_index()
	{
		$limit  = 50;
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;

		$contacts = ORM::factory('Contact')
			->where('contact_type_id', 'IN', array(Model_Contact_Type::MOBILE, Model_Contact_Type::PHONE));

		$clone_to_count = clone $contacts;
		$count_all = $clone_to_count->count_all();

		$contacts->offset($offset)
			->limit($limit)
			->where('verified', '=', 0)
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

	public function action_moderate()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$json = array('code' => 200);

		$contact = ORM::factory('Contact', $this->request->param('id'));
		if ( ! $contact->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$verified = (int) $this->request->post('verified');

		$contact->verified = ($verified ? 1 : -1);
		$contact->save();

		$this->response->body(json_encode($json));
	}
}

/* End of file Phones.php */
/* Location: ./application/classes/Controller/Admin/Phones.php */