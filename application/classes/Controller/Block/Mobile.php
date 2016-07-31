<?php

    class Controller_Block_Mobile extends Controller_Block {

        public function before() {
            parent::before();

            $this->auto_render = false;
        }

        public function action_menu()
        {
            $twig = Twig::factory('block/header/mobile_menu/index');
            $this->response->body($twig);
        }

    }

