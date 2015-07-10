<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Search_Landing extends Controller_Search {

	public function action_avtotransport() {

        $twig = Twig::factory('landing/avtotransport');

        $twig->domain      = $this->domain;
        $twig->city        = $this->domain->get_city();
        
        $this->response->body($twig);
    }
}
