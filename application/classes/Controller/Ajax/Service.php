<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest_Service extends Controller_Rest {

	public function action_up()
	{
		$ad = ORM::factory('Object', intval($this->request->param('id')));
		if ( ! $ad->loaded() OR ! Auth::instance()->get_user() OR $ad->author != Auth::instance()->get_user()->id)
		{
			throw new HTTP_Exception_404;
		}

		$info = Object::canEdit(Array("object_id" => $ad->id, "rubricid" => $ad->category));

		if ( $info["code"] == "error" )
		{
			$this->json['code'] = 400;
			$this->json['errors'] = $info["errors"];
		}
		elseif ($ad->get_service_up_timestamp() > time())
		{
			$this->json['code'] = 400;
			$this->json['date_service_up_available'] = date("d.m Y в H:i", $ad->get_service_up_timestamp());
		}
		else
		{
			$ad->up();
			$this->json['date_service_up_available'] = date("d.m Y в H:i", $ad->get_service_up_timestamp());
		}
	}

}
