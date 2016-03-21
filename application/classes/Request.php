<?php defined('SYSPATH') OR die('No direct script access.');

class Request extends Kohana_Request {


	public function get_full_url() {
		return "http://".$_SERVER['HTTP_HOST']."/".$this->uri();
	}

	public function set_param($name, $value) {
		$this->_params[$name] = $value;
	}
}