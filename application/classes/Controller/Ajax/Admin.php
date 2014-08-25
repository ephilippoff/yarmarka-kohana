<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin extends Controller_Ajax {

	public function action_relation_save()
	{
		$data = $this->request->post();
echo Debug::vars($data);
		$ar = ORM::factory('Attribute_Relation');
		$ar->category_id 			= $data["category_id"];
		$ar->reference_id 			= $data["reference_id"];
		if ($data["parent_id"])
			$ar->parent_id 				= $data["parent_id"];
		if ($data["parent_element_id"])
			$ar->parent_element_id 		= $data["parent_element_id"];
		if ($data["options"])
			$ar->options 			= $data["options"];
		if ($data["custom"])
			$ar->custom 				= $data["custom"];
		if ($data["is_required"])
			$ar->is_required 				= ($data["is_required"] == "on") ? 1:0;
		if ($data["weight"])
			$ar->weight 				= $data["weight"];
		$ar->save();
	}

	function action_relation_delete()
	{
		$data = $this->request->post();
		DB::delete('attribute_relation')
			->where('id', '=', $data["id"])
			->execute();
	}
}