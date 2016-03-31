<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {
 
    public function get_response()
    {
		if (Kohana::$environment === Kohana::DEVELOPMENT)
		{
			return parent::get_response();
		}
		else
		{
			return Response::factory()->status(404)->body(Request::factory('block/error_404')->execute());
		}
    }
}
