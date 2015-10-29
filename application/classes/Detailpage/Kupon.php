<?php defined('SYSPATH') or die('No direct script access.');

class Detailpage_Kupon extends Detailpage_Default
{
	protected $search_info = NULL;

	public function __construct($object)
	{
		parent::__construct($object);
	}

	public function get_kupon_info()
	{
		$object = $this->_orm_object;
		$info = array();
		$info['kupon_info'] = array();

		$subq_sold = DB::select(DB::expr("count(id)"))
					->from("kupon")
					->where("kupon.kupon_group_id", "=",DB::expr("kupon_group.id"))
					->where("kupon.state","=","sold");

		$subq_avail = DB::select(DB::expr("count(id)"))
					->from("kupon")
					->where("kupon.kupon_group_id", "=",DB::expr("kupon_group.id"))
					->where("kupon.state","=","avail");

		$subq_reserve = DB::select(DB::expr("count(id)"))
					->from("kupon")
					->where("kupon.kupon_group_id", "=",DB::expr("kupon_group.id"))
					->where("kupon.state","=","reserve");

		$info['kupon_info']["groups"] = ORM::factory("Kupon_Group")
										->select(array($subq_sold, "sold_count"), array($subq_avail, "avail_count"), array($subq_reserve, "reserve_count"))
										->where("kupon_group.object_id","=",$object->id)
										->getprepared_all();

		$info['kupon_info']["sold_count"] = array_sum(array_map(function($item){ return $item->sold_count; }, $info['kupon_info']["groups"]));
		$info['kupon_info']["avail_count"] = array_sum(array_map(function($item){ return $item->avail_count; }, $info['kupon_info']["groups"]));


		$info['kupons_buy_access'] = ($info['kupon_info']["avail_count"]) ? TRUE : FALSE;

		$this->_info = array_merge($this->_info, $info);
		return $this;
	}

}