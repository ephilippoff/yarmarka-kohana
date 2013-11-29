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
}