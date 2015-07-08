<?php defined('SYSPATH') OR die('No direct script access.');

class Request extends Kohana_Request {


	public function get_parsed_uri() {
		return $this->uri();
	}
}