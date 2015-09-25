<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User_Profile extends Controller_Template {

    var $user; // current user
    
    public function before()
    {
        parent::before();
        $this->use_layout   = FALSE;
        $this->auto_render  = FALSE;

        if ( ! $this->user = Auth::instance()->get_user())
        {
            $this->redirect(URL::site('user/login?return='.$this->request->uri()));
        } else {
            $this->user->reload();
            if ($this->user->is_blocked == 1)
            {
                $this->redirect(URL::site('user/message?message=userblock'));
            }
        }

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
        // if ($this->user AND !$this->user->is_valid_orginfo()
        //             AND in_array(Request::current()->action(), array('edit_ad','objectload','priceload','published')))
        //         {
        //             if ($this->user->is_expired_date_validation())
        //                 HTTP::redirect("/user/orginfo?from=another");
        //         }
    }

    public function action_orginfo()
    {
        $twig = Twig::factory('user/orginfo');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/orginfo";
        $twig->user = $this->user;
        $twig->block_name = "user/_orginfo";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Данные компании"),
        );

        $user = $this->user;

        if ($user->org_type <> 2){
            $this->redirect("/user/userinfo");
            return;
        }

        

        // if ($user->linked_to_user) {
        //     $this->template = View::factory('user/ischilduser', array("company"=> ORM::factory('User', $user->linked_to_user),
        //                                                                 "name" => "Информация о компании"));
        //     return;
        // }

        $is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
        $data = $inn =NULL;
        $errors = new Obj();

        $form = Form_Custom::factory("Orginfo");

        $settings = new Obj(ORM::factory('User_Settings')
                                ->get_group($user->id, "orginfo"));
        if ($user->org_inn)
        {
            unset($form->_settings["fields"]["INN"]);
            unset($form->_settings["fields"]["INN_photo"]);
            unset($form->_settings["fields"]["org_full_name"]);
            $inn_skan = Imageci::getSitePaths($user->org_inn_skan);
            $inn = array(
                    "inn"           => $user->org_inn,
                    "org_full_name" => $user->org_full_name,
                    "inn_skan"      => $inn_skan["120x90"]
                );
        } 

        $inn_moderate = array(
            "inn_moderate"          => $user->org_moderate,
            "inn_moderate_reason"   => $settings->{"moderate-reason"}
        );

        if ($is_post)
        {
            $data       = $this->request->post();

            if (isset($data["INN"])){
                $parentuser = ORM::factory('User')
                                ->where("org_moderate","=",1)
                                ->where("org_inn","=",$data["INN"])
                                ->where("id","<>",$user->id)
                                ->find();

                if ($parentuser->loaded())
                    $this->redirect("/user/user_link_request?inn=".$data["INN"]);
            }

            
            try
            {
                $db = Database::instance();
                $db->begin();
                $form->save($data);
                $db->commit();
            }
            catch(Exception $e)
            {
                $db->rollback();
                Admin::send_error("Ошибка при сохранении формы компании", array(
                        $e->getMessage(), Debug::vars($data), $e->getTraceAsString()
                ));
                return;
            }



            if ($form->errors)
            {
                $errors = new Obj($form->errors);
            } 
            else 
            {
                try
                {
                    $db = Database::instance();
                    $db->begin();

                    if ( array_key_exists("INN", $data) )
                    {
                        //прописываем инн, скан и юр имя организации в User
                        $user->org_inn = $data["INN"];
                        $user->org_inn_skan = $data["INN_photo"];
                        $user->org_full_name = $data["org_full_name"];
                        $user->org_moderate = 0;

                        //ставим на модерацию
                        ORM::factory('User_Settings')
                            ->update_or_save($user->id, "orginfo", "moderate", 0);

                        //удаляем причину модерации, если она была проставлена ранее
                        ORM::factory('User_Settings')
                            ->_delete($user->id, "orginfo", "moderate-reason");
                    }

                    $user->org_name         = $data["org_name"];
                    $user->org_post_address = $data["mail_address"];
                    $user->org_phone        = $data["phone"];
                    $user->about = $data["commoninfo"];
                    $user->filename = ORM::factory('User_Settings')
                                            ->where("user_id","=",$user->id)
                                            ->where("name","=","logo")
                                            ->where("type","=","orginfo")
                                            ->find()
                                            ->value;
                    $user->save();

                    $db->commit();
                }
                catch(Exception $e)
                {
                    $db->rollback();
                    Admin::send_error("Ошибка при сохранении ИНН", array(
                            $e->getMessage(), Debug::vars($data), $e->getMessage()
                    ));
                    $this->redirect('/user/orginfo?error=1');
                    return;
                }

                $this->redirect('/user/orginfo?success=1');
            }
        }
        else 
        {
            $data = $form->get_data();
        }

        $twig->params->expired = $settings->{"date-expired"};
        $twig->params->from = $this->request->query("from");
        $twig->params->form = $form->prerender($data);
        $twig->params->data = new Obj($data);
        $twig->params->errors = $errors;
        $twig->params->inn = $inn;
        $twig->params->inn_moderate = $inn_moderate;
        $twig->params->success = $this->request->query("success");
        $twig->params->org_moderate_states = Kohana::$config->load("dictionaries.org_moderate_states");
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_userinfo()
    {
        $twig = Twig::factory('user/userinfo');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/userinfo";
        $twig->user = $this->user;
        $twig->block_name = "user/_userinfo";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Данные пользователя"),
        );

        $user = $this->user;
        $is_post = ($_SERVER['REQUEST_METHOD'] == 'POST');
        $data = NULL;
        $errors = new Obj();
        $form = Form_Custom::factory("Userinfo");


        if ($is_post)
        {
            $data = $this->request->post();
            $form->save($data);
            if ($form->errors)
            {
                $errors = new Obj($form->errors);
            } else {
                $user->fullname = $data["contact_name"];
                $user->save();
            }
        }
        else
        {
            $data = $form->get_data();
        }

        $twig->params->categories_limit = ORM::factory('Category')
                                                ->get_limited()
                                                ->find_all();

        $twig->params->individual_limit = ORM::factory('Category')
                                                ->get_individual_limited($user->id);

        $twig->params->request_company = ORM::factory('User_Link_Request')
                                                ->where("linked_user_id","=",$user->id)
                                                ->find();   

        $twig->params->types = Kohana::$config->load("dictionaries.org_types");
        $twig->params->user = $user;
        $twig->params->form = $form->prerender($data);
        $twig->params->errors = $errors;
        $twig->params->parent_user = ORM::factory('User', $user->linked_to_user);
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_password()
    {
        $twig = Twig::factory('user/password');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/password";
        $twig->user = $this->user;
        $twig->block_name = "user/_password";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Смена пароля"),
        );

        $user = $this->user;
        
        $error = NULL;

        if (HTTP_Request::POST === $this->request->method())
        {
            $validation = Validation::factory($_POST)
                ->rule('password', 'not_empty')
                ->rule('password', 'min_length', array(':value', 6))
                ->label('password', 'Пароль')
                ->rule('password', 'matches', array(':validation', 'password', 'password_repeat'));

            if ($validation->check())
            {
                $this->user->passw = trim($this->request->post('password'));
                $this->user->save();

                Session::instance()->set('success', TRUE);
            }
            else
            {
                $error = join(',', $validation->errors('validation/password'));
            }
        }

        $twig->params->error = $error;
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_employers()
    {
        $twig = Twig::factory('user/employers');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/employers";
        $twig->user = $this->user;
        $twig->block_name = "user/_employers";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Сотрудники (подчиненные учетные записи)"),
        );

        $user = $this->user;

        if ($user->org_type <> 2){
            $this->redirect("/user/userinfo");
            return;
        }
        if ($user->org_moderate <> 1){
            $this->redirect("/user/orginfo");
            return;
        }

        $error = NULL;
        
        $is_post = (HTTP_Request::POST === $this->request->method());
        $email = trim(mb_strtolower($this->request->post('email')));        
        $method = $this->request->query('method');
        $actionuser_id = (int) $this->request->query('id');     

        
        if ($is_post AND $method == "link")
        {
            $childuser_add = ORM::factory('User')
                                ->where("email","=",mb_strtolower($email))
                                ->where("id","<>",$user->id)
                                ->find()
                                ->link_user($user->id);

            if (!$childuser_add)
            {
                $this->redirect("/user/employers?success=1");
            } else {
                $error = $childuser_add;
            }

        } elseif ($method == "accept_request"){

            $childuser = ORM::factory('User',$actionuser_id);
            $childuser_add = $childuser->link_user($user->id, TRUE);
            if (!$childuser_add)
            {
                $msg = View::factory('emails/user_manage/accept_request_to_link_company', 
                    array(
                        'request_user' => $user,
                    )
                );
                Email::send($childuser->email, Kohana::$config->load('email.default_from'), "Привязка к компании ".$user->org_name." подтверждена", $msg);
                
                ORM::factory('User_Link_Request')->delete_request($user->id, $actionuser_id);
                $this->redirect("/user/employers?success=1");
            } else {
                $error = $childuser_add;
            }   

        } elseif ($method == "unlink"){

            ORM::factory('User')->unlink_user($user->id, $actionuser_id);

        } elseif ($method == "decline_request"){
            $childuser = ORM::factory('User',$actionuser_id);
            $msg = View::factory('emails/user_manage/decline_request_to_link_company', 
                    array(
                        'request_user' => $user,
                    )
                );
            Email::send($childuser->email, Kohana::$config->load('email.default_from'), "Привязка к компании ".$user->org_name." НЕ подтверждена", $msg);
                
            ORM::factory('User_Link_Request')->decline_request($user->id, $actionuser_id);              

        }

        $twig->params->is_post = $is_post; 
        $twig->params->error = $error;
        $twig->params->users = ORM::factory('User')
                                        ->where("linked_to_user","=", $user->id)
                                        ->find_all();

        $twig->params->requests = ORM::factory('User_Link_Request')
                                        ->where("user_id","=", $user->id)
                                        ->find_all();

        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_contacts()
    {
        $twig = Twig::factory('user/contacts');
        $twig->city = $this->domain->get_city();
        $twig->canonical_url = "user/contacts";
        $twig->user = $this->user;
        $twig->block_name = "user/_contacts";
        $twig->params = new Obj();

        $twig->crumbs = array(
            array("title" => "Личный кабинет - Управление контактами"),
        );

        $user = $this->user;
   
        $twig->params->user_contacts  = ORM::factory('Contact')
            ->select('contact_type.name')
            ->with('contact_type')
            ->where_user_id($user->id)
            ->where('verified_user_id', '=', $user->id)
            ->order_by('id')
            ->find_all();
        
        $twig->params->user           = $user;
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_user_link_request()
    {
        $twig = Twig::factory('user/link_request');
        $twig->block_name = "user/_link_request";
        $twig->params = new Obj();

        $user = Auth::instance()->get_user();

        $method = $this->request->query("method");
        $parentuser_inn = $this->request->query("inn");
        $parentuser_email = $this->request->query("email");
        
        if ($method == "delete_request")
        {
            ORM::factory('User_Link_Request')
                ->delete_requests($user->id);

            $this->redirect("/user/userinfo");
        }
        if (!$parentuser_inn AND !$parentuser_email)
            $this->redirect("/user/orginfo");

        $request_type = NULL;
        if ($parentuser_email)
        {
            $parentuser = ORM::factory('User')
                                ->where("org_moderate","=",1)
                                ->where("email","=",trim(mb_strtolower($parentuser_email)))
                                ->where("id","<>",$user->id)
                                ->find();
            $request_type = "email";
        } else {
            $parentuser = ORM::factory('User')
                                ->where("org_moderate","=",1)
                                ->where("org_inn","=",$parentuser_inn)
                                ->where("id","<>",$user->id)
                                ->find();
            $request_type = "inn";
        }

        if (!$parentuser->loaded())
            $this->redirect("/user/userinfo");

        $is_post = ($_SERVER['REQUEST_METHOD']=='POST');
        $ulr = NULL;
        if ($is_post) { 
            $parentuser_id = $this->request->post("id");

            $ulr = ORM::factory('User_Link_Request')
                        ->where("linked_user_id","=",$user->id)
                        ->find();
            $ulr->user_id = $parentuser_id;
            $ulr->linked_user_id = $user->id;
            $ulr->save();

            $msg = View::factory('emails/user_manage/request_to_link_company', 
                array(
                    'request_user' => $user,
                )
            );
            Email::send($parentuser->email, Kohana::$config->load('email.default_from'), "Запрос на разрешение подачи объявлений от лица вашей компании", $msg);
        } else {
            $ulr = ORM::factory('User_Link_Request')
                        ->where("linked_user_id","=",$user->id)
                        ->find();
        }

        $twig->params->request_type = $request_type;
        $twig->params->inn = $parentuser_inn;
        $twig->params->email = $parentuser_email;
        $twig->params->parentuser = $parentuser;
        $twig->params->request = $ulr;
        $twig->params = (array) $twig->params;
        $this->response->body($twig);
    }

    public function action_orginfoinn_decline_user()
    {
        $this->auto_render = FALSE;
        $json = array('code' => 400);

        $user = $this->user;
        if (!$user OR $user->org_moderate == 1)
        {
            throw new HTTP_Exception_404;
        }

        $user->org_inn       = NULL;
        $user->org_inn_skan  = NULL;
        $user->org_full_name = NULL;
        $user->org_moderate = NULL;
        $user->estimate = NULL;
        $user->save();

        $setting = ORM::factory('User_Settings')
                        ->where("user_id","=",$user->id)
                        ->where("name","=","moderate")
                        ->where("type","=","orginfo")
                        ->delete_all();

        $setting = ORM::factory('User_Settings')
                        ->where("user_id","=",$user->id)
                        ->where("name","=","moderate-reason")
                        ->where("type","=","orginfo")
                        ->delete_all();

        $this->redirect("/user/orginfo");
    }
}