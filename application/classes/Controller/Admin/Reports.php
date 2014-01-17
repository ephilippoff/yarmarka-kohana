<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Reports extends Controller_Admin_Template {
	
	protected $module_name = 'reports';
	
	public function action_operstat()
	{			
		
			$date = $this->request->query('date');
			
			$from_time = strtotime($date['from']) ? strtotime($date['from']) : strtotime(date('Y-m-d'));
			$to_time   = strtotime($date['to']) ? strtotime($date['to']) : strtotime(date('Y-m-d'));
		
			//$operstat = ORM::factory('object_moderation_log')->find_all()->as_array();
			
			$operstat = DB::select('action_by', 'user.email', 'fullname', DB::expr('COUNT(action_by)'))
					->from('object_moderation_log')
					->join('user', 'LEFT')
					->on('object_moderation_log.action_by', '=', 'user.id')
					->where('user.role', '=', 3)
					->where(DB::expr('date(createdon)'), '>=', DB::expr("date '".date('Y-m-d', $from_time)."'"))
					->where(DB::expr('date(createdon)'), '<=', DB::expr("date '".date('Y-m-d', $to_time)."'"))
					->group_by('object_moderation_log.action_by', 'user.email', 'user.fullname')
					->order_by('count', 'DESC')
					->execute();
		
			$this->template->operstat = $operstat;
	}
	
	public function action_oper_objects_list()
	{
			$date = $this->request->query('date');
			
			$from_time = strtotime($date['from']) ? strtotime($date['from']) : strtotime(date('Y-m-d'));
			$to_time   = strtotime($date['to']) ? strtotime($date['to']) : strtotime(date('Y-m-d'));
			
			$limit  = Arr::get($_GET, 'limit', 50);
			$page   = $this->request->query('page');
			$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;			

			$query = DB::select()
					->from('object_moderation_log')
					->join('object', 'LEFT')->on('object_moderation_log.object_id', '=', 'object.id')
					->join(array('user', 'op'), 'LEFT')->on('object_moderation_log.action_by', '=', 'op.id')
					->join(array('user', 'author'), 'LEFT')->on('object_moderation_log.user_id', '=', 'author.id')
					->join('category', 'LEFT')->on('object.category', '=', 'category.id')
					->join('object_attachment', 'LEFT')->on('object.main_image_id', '=', 'object_attachment.id')
					->where(DB::expr('date(object_moderation_log.createdon)'), '>=', DB::expr("date '".date('Y-m-d', $from_time)."'"))
					->where(DB::expr('date(object_moderation_log.createdon)'), '<=', DB::expr("date '".date('Y-m-d', $to_time)."'"))
					->where('object.source_id', '=', 1)
					->where('object.active', '=', 1);			
			
			
			if ($operator_id = intval($this->request->query('operator_id')))
			{
				$query->where('object_moderation_log.action_by', '=', $operator_id);
			}		
			
			if ($object_id = intval($this->request->query('object_id')))
			{
				$query->where('object_moderation_log.object_id', '=', $object_id);
			}
			
			$query_total = clone $query;												
			
			$query->select('object_moderation_log.object_id', 
					'object_moderation_log.id' , 
					'object_moderation_log.createdon', 
					'object_moderation_log.action_by', 
					'object_moderation_log.user_id', 
					'object_moderation_log.description', 
					'object.title', 
					'object.user_text', 
					'object.full_text', 
					'object.date_created', 
					'object.real_date_created', 
					'object.is_bad', 
					'object.is_published', 
					array('op.email', 'op_email'), 
					array('op.fullname', 'op_fullname'), 
					array('author.email', 'author_email'),
					array('category.title', 'category_title'),
					array('object_attachment.filename', 'object_main_photo'));
			
			$query->order_by('createdon', 'DESC');
			$logs = $query->limit($limit)->offset($offset)->execute();
			
			$query_total->select(array(DB::expr('COUNT(*)'), 'logs_total'));
			$logs_total = $query_total->execute();
										
			
			$this->template->logs = $logs;
			$this->template->total = $logs_total[0]['logs_total'];
			$this->template->operators = DB::select('id', 'fullname')->from('user')->where('role', '=', 3)->execute()->as_array('id', 'fullname');
			
			$this->template->limit = $limit;
			$this->template->pagination	= Pagination::factory(array(
					'current_page'   => array('source' => 'query_string', 'key' => 'page'),
					'total_items'    => $logs_total[0]['logs_total'],
					'items_per_page' => $limit,
					'auto_hide'      => TRUE,
					'view'           => 'pagination/bootstrap',
				))->route_params(array(
					'controller' => 'reports',
					'action'     => 'oper_objects_list',
				));			
			
	}
	
	
}