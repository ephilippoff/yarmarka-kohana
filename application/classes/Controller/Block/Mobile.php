<?php

    class Controller_Block_Mobile extends Controller_Template {

        public function before() {
            //parent::before();

            $this->auto_render = false;
        }

        public function action_menu()
        {

            $twig = Twig::factory('block/header/mobile_menu/index');
            $twig->request_uri = $this->request->query('location');
            $twig->user = Auth::instance()->get_user();
            $this->response->body($twig);
        }

    }

