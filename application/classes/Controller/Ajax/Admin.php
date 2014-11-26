<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin extends Controller_Ajax {

	public function action_relation_save()
	{
		$data = $this->request->post();

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
			$ar->is_required 				= ($data["is_required"] == "true") ? 1:0;
		if ($data["weight"])
			$ar->weight 				= $data["weight"];
		$ar->save();
	}
	
	public function action_relation_update()
	{
		$data = $this->request->post();

		$ar = ORM::factory('Attribute_Relation', $data['id']);

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
			$ar->is_required 				= ($data["is_required"] == "true") ? 1:0;
		if ($data["weight"])
			$ar->weight 				= $data["weight"];
		$ar->update();
	}	

	function action_relation_delete()
	{
		$data = $this->request->post();
		DB::delete('attribute_relation')
			->where('id', '=', $data["id"])
			->execute();
	}

	function action_orginfo_moderate()
	{
		$user_id = $this->request->post("user_id");
		$method  = $this->request->post("method");
		$message = $this->request->post("message");

		$user = ORM::factory('User',$user_id);

		if (!$user->loaded())
			return;

		if ($method == "ok")
		{		
			$user->org_moderate = 1;
			$user->save();

			$setting = ORM::factory('User_Settings')
							->where("user_id","=",$user_id)
							->where("type","=","orginfo")
							->where("name","=","moderate")
							->find();

			$setting->value = 1;
			$setting->save();

			ORM::factory('User_Settings')
							->where("user_id","=",$user_id)
							->where("type","=","orginfo")
							->where("name","=","moderate-reason")
							->delete_all();
		} elseif ($method == "cancel"){

			$user->org_moderate = 2;
			$user->save();

			$user = ORM::factory('User', $user_id);

			$user->org_inn 		 = NULL;
			$user->org_inn_skan  = NULL;
			$user->org_full_name = NULL;
			$user->save();

			$setting = ORM::factory('User_Settings')
							->where("user_id","=",$user_id)
							->where("type","=","orginfo")
							->where("name","=","moderate")
							->find();
			$setting->value = 2;
			$setting->save();

			if ($message)
			{
				$setting = ORM::factory('User_Settings')
								->where("user_id","=",$user_id)
								->where("type","=","orginfo")
								->where("name","=","moderate-reason")
								->find();

				$setting->value = $message;
				$setting->save();
			}


		}
	}


}