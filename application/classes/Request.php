<?php defined('SYSPATH') OR die('No direct script access.');

class Request extends Kohana_Request {


	public function get_full_url() {
		return "http://".$_SERVER['HTTP_HOST']."/".$this->uri();
	}
}